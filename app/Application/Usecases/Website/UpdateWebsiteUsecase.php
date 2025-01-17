<?php

namespace App\Application\Usecases\Website;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Domain\Entities\Website;
use App\Domain\Repositories\WebsiteRepositoryInterface;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Application\Usecases\File\CreateFileUsecase;
use App\Application\Usecases\File\DeleteFileUsecase;
use App\Exceptions\UnauthorizedException;

class UpdateWebsiteUsecase
{
    public function __construct(
        private WebsiteRepositoryInterface $websiteRepository,
        private RestaurantRepositoryInterface $restaurantRepository,
        private CreateFileUsecase $createFileUsecase,
        private DeleteFileUsecase $deleteFileUsecase
    ) {
    }

    public function execute(string $restaurantId, array $data): Website
    {
        return DB::transaction(function () use ($restaurantId, $data) {
            try {
                // 1. Auth
                $user = Auth::user();
                if (!$user) {
                    throw new UnauthorizedException("User not authenticated.");
                }

                // 2. Retrouver le website
                $website = $this->websiteRepository->findByRestaurantId($restaurantId);
                if (!$website) {
                    throw new \Exception("Website not found.");
                }

                // 3. Vérifier les permissions
                if (!in_array($user->role, ['admin', 'franchise_manager'])) {
                    $restaurant = $this->restaurantRepository->findByOwnerId($user->id);
                    if (!$restaurant || $restaurant->getId() !== $restaurantId) {
                        throw new UnauthorizedException("You do not have permission to update this website.");
                    }
                }

                // 4. Gérer l'image de présentation
                if (isset($data['presentation_image'])) {
                    // Supprimer l'ancien fichier si existe
                    if ($website->getPresentationImageId()) {
                        $this->deleteFileUsecase->execute($website->getPresentationImageId());
                    }

                    // Créer le nouveau fichier
                    $folderUuid = Str::uuid()->toString();
                    $securePath = "websites/images/{$folderUuid}";
                    $file = $this->createFileUsecase->execute($data['presentation_image'], $securePath);
                    
                    // Mettre à jour l'ID dans le website
                    $website->setPresentationImageId($file->getId());
                } elseif (isset($data['remove_presentation_image']) && $data['remove_presentation_image'] === 'true') {
                    if ($website->getPresentationImageId()) {
                        $this->deleteFileUsecase->execute($website->getPresentationImageId());
                        $website->setPresentationImageId(null);
                    }
                }

                // 5. Mettre à jour les autres champs
                if (isset($data['title'])) {
                    $website->setTitle($data['title']);
                }
                if (isset($data['description'])) {
                    $website->setDescription($data['description']);
                }
                if (isset($data['domain'])) {
                    $website->setDomain($data['domain']);
                }
                if (isset($data['opening_hours'])) {
                    $website->setOpeningHours($data['opening_hours']);
                }
                if (isset($data['theme_config'])) {
                    $website->setThemeConfig($data['theme_config']);
                }

                // 6. Sauvegarder
                return $this->websiteRepository->update($website);
            } catch (\Exception $e) {
                throw $e;
            }
        });
    }
} 