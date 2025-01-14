<?php

namespace App\Application\Usecases\MenuItem;

use Illuminate\Support\Facades\Auth;
use App\Domain\Entities\MenuItem;
use App\Domain\Repositories\MenuItemRepositoryInterface;
use App\Domain\Repositories\MenuCategoryRepositoryInterface;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Exceptions\UnauthorizedException;

class UpdateMenuItemUsecase
{
    public function __construct(
        private MenuItemRepositoryInterface $menuItemRepository,
        private MenuCategoryRepositoryInterface $menuCategoryRepository,
        private MenuRepositoryInterface $menuRepository,
        private RestaurantRepositoryInterface $restaurantRepository
    ) {
    }

    public function execute(string $menuItemId, array $data): MenuItem
    {
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

        // 6. Mettre à jour l'entité
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
        if (array_key_exists('images', $data)) {
            $existingItem->setImages($data['images']);
        }
        if (isset($data['sort_order'])) {
            $existingItem->setSortOrder($data['sort_order']);
        }
        if (isset($data['is_active'])) {
            $existingItem->setIsActive($data['is_active']);
        }

        // 7. Persister
        return $this->menuItemRepository->update($existingItem);
    }
} 