<?php

namespace App\Application\Usecases\Menu;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Application\Usecases\File\CreateFileUsecase;
use App\Application\Usecases\File\DeleteFileUsecase;
use App\Domain\Entities\Menu;
use App\Exceptions\UnauthorizedException;
use App\Services\S3Service;

class UpdateMenuUsecase
{
    public function __construct(
        private MenuRepositoryInterface $menuRepository,
        private RestaurantRepositoryInterface $restaurantRepository,
        private CreateFileUsecase $createFileUsecase,
        private DeleteFileUsecase $deleteFileUsecase,
        private S3Service $s3Service
    ) {}

    public function execute(string $menuId, array $data): Menu
    {
        return DB::transaction(function () use ($menuId, $data) {
            try {
                // 1. Auth
                $user = Auth::user();
                if (!$user) {
                    throw new UnauthorizedException("User not authenticated.");
                }

                // 2. Retrouver le menu
                $menu = $this->menuRepository->findById($menuId);
                if (!$menu) {
                    throw new \Exception("Menu not found.");
                }

                // 3. Vérifier les permissions
                if (!in_array($user->role, ['admin', 'franchise_manager'])) {
                    $restaurant = $this->restaurantRepository->findByOwnerId($user->id);
                    if (!$restaurant || $restaurant->getId() !== $menu->getRestaurantId()) {
                        throw new UnauthorizedException("You do not have permission to update this menu.");
                    }
                }

                // 4. Gérer les bannières
                $currentBanners = $menu->getBanners() ?? [];
                $newBanners = [];
                $bannersToDelete = [];

                // 4.1 Supprimer les bannières demandées
                if (isset($data['remove_banners']) && is_array($data['remove_banners'])) {
                    foreach ($currentBanners as $banner) {
                        if (!in_array($banner['id'], $data['remove_banners'])) {
                            $newBanners[] = $banner;
                        } else {
                            $bannersToDelete[] = $banner;
                        }
                    }
                } else {
                    $newBanners = $currentBanners;
                }

                // 4.2 Ajouter les nouvelles bannières
                if (isset($data['banners']) && is_array($data['banners'])) {
                    $folderUuid = Str::uuid()->toString();
                    $securePath = "menus/banners/{$folderUuid}";
                    
                    foreach ($data['banners'] as $banner) {
                        $file = $this->createFileUsecase->execute($banner, $securePath);
                        $newBanners[] = [
                            'id' => $file->getId(),
                            'url' => $file->getUrl()
                        ];
                    }
                }

                // 5. Mettre à jour les autres champs
                if (isset($data['name'])) {
                    $menu->setName($data['name']);
                }
                if (isset($data['status'])) {
                    $menu->setStatus($data['status']);
                }

                // Mettre à jour les bannières
                $menu->setBanners($newBanners);

                // 6. Persister les changements
                $updatedMenu = $this->menuRepository->update($menu);

                // 7. Supprimer les anciennes bannières
                foreach ($bannersToDelete as $banner) {
                    $this->deleteFileUsecase->execute($banner['id']);
                }

                return $updatedMenu;

            } catch (\Exception $e) {
                // En cas d'erreur, nettoyer les nouvelles bannières si créées
                if (isset($newBanners) && !empty($newBanners)) {
                    foreach ($newBanners as $banner) {
                        if (!in_array($banner, $currentBanners)) {
                            $this->s3Service->deleteFile($banner['url']);
                        }
                    }
                }
                throw $e;
            }
        });
    }
} 