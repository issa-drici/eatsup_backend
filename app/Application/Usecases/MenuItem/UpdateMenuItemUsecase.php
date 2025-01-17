<?php

namespace App\Application\Usecases\MenuItem;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Domain\Entities\MenuItem;
use App\Domain\Repositories\MenuItemRepositoryInterface;
use App\Domain\Repositories\MenuCategoryRepositoryInterface;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Application\Usecases\File\CreateFileUsecase;
use App\Application\Usecases\File\DeleteFileUsecase;
use App\Exceptions\UnauthorizedException;
use App\Services\S3Service;
use Illuminate\Support\Str;

class UpdateMenuItemUsecase
{
    public function __construct(
        private MenuItemRepositoryInterface $menuItemRepository,
        private MenuCategoryRepositoryInterface $menuCategoryRepository,
        private MenuRepositoryInterface $menuRepository,
        private RestaurantRepositoryInterface $restaurantRepository,
        private CreateFileUsecase $createFileUsecase,
        private DeleteFileUsecase $deleteFileUsecase,
        private S3Service $s3Service
    ) {}

    public function execute(string $menuItemId, array $data): MenuItem
    {
        return DB::transaction(function () use ($menuItemId, $data) {
            try {
                // 1. Auth
                $user = Auth::user();
                if (!$user) {
                    throw new UnauthorizedException("User not authenticated.");
                }

                // 2. Rôle
                if (!in_array($user->role, ['admin', 'restaurant_owner', 'franchise_manager'])) {
                    throw new UnauthorizedException("User not allowed to update menu items.");
                }

                // 3. Retrouver l'item
                $existingItem = $this->menuItemRepository->findById($menuItemId);
                if (!$existingItem) {
                    throw new \Exception("Menu item not found.");
                }

                // 4. Vérifier la chaîne de propriété (item -> category -> menu -> restaurant)
                $category = $this->menuCategoryRepository->findById($existingItem->getCategoryId());
                if (!$category) {
                    throw new \Exception("Menu category not found.");
                }

                $menu = $this->menuRepository->findById($category->getMenuId());
                if (!$menu) {
                    throw new \Exception("Menu not found.");
                }

                // 5. Si l'utilisateur n'est pas admin ou franchise_manager, vérifier qu'il est propriétaire
                if (!in_array($user->role, ['admin', 'franchise_manager'])) {
                    $restaurant = $this->restaurantRepository->findByOwnerId($user->id);
                    if (!$restaurant || $restaurant->getId() !== $menu->getRestaurantId()) {
                        throw new UnauthorizedException("You do not have permission to update this menu item.");
                    }
                }

                // 6. Gérer les images
                $currentImages = $existingItem->getImages() ?? [];
                $newImages = [];
                $imagesToDelete = [];

                // 6.1 Supprimer les images demandées
                if (isset($data['remove_images']) && is_array($data['remove_images'])) {
                    foreach ($currentImages as $image) {
                        if (!in_array($image['id'], $data['remove_images'])) {
                            $newImages[] = $image;
                        } else {
                            $imagesToDelete[] = $image;
                        }
                    }
                } else {
                    $newImages = $currentImages;
                }

                // 6.2 Ajouter les nouvelles images
                if (isset($data['images']) && is_array($data['images'])) {
                    $folderUuid = Str::uuid()->toString();
                    $securePath = "menu-items/images/{$folderUuid}";
                    
                    foreach ($data['images'] as $image) {
                        $file = $this->createFileUsecase->execute($image, $securePath);
                        $newImages[] = [
                            'id' => $file->getId(),
                            'url' => $file->getUrl()
                        ];
                    }
                }

                // 7. Mettre à jour les autres champs
                if (isset($data['name'])) {
                    $existingItem->setName($data['name']);
                }
                if (array_key_exists('description', $data)) {
                    $existingItem->setDescription($data['description']);
                }
                if (isset($data['price'])) {
                    $existingItem->setPrice($data['price']);
                }
                if (array_key_exists('allergens', $data)) {
                    $existingItem->setAllergens($data['allergens']);
                }
                if (isset($data['sort_order'])) {
                    $existingItem->setSortOrder($data['sort_order']);
                }
                if (isset($data['is_active'])) {
                    $existingItem->setIsActive($data['is_active']);
                }

                // Mettre à jour les images
                $existingItem->setImages($newImages);

                // 8. Persister les changements
                $updatedItem = $this->menuItemRepository->update($existingItem);

                // 9. Supprimer les anciennes images
                foreach ($imagesToDelete as $image) {
                    $this->deleteFileUsecase->execute($image['id']);
                }

                return $updatedItem;

            } catch (\Exception $e) {
                // En cas d'erreur, nettoyer les nouvelles images si créées
                if (isset($newImages) && !empty($newImages)) {
                    foreach ($newImages as $image) {
                        if (!in_array($image, $currentImages)) {
                            $this->s3Service->deleteFile($image['url']);
                        }
                    }
                }
                throw $e;
            }
        });
    }
} 