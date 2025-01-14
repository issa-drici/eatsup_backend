<?php

namespace App\Application\Usecases\MenuItem;

use Illuminate\Support\Facades\Auth;
use App\Domain\Entities\MenuItem;
use App\Domain\Repositories\MenuItemRepositoryInterface;
use App\Domain\Repositories\MenuCategoryRepositoryInterface;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Exceptions\UnauthorizedException;

class UpdateMenuItemMoveDownUsecase
{
    public function __construct(
        private MenuItemRepositoryInterface $menuItemRepository,
        private MenuCategoryRepositoryInterface $menuCategoryRepository,
        private MenuRepositoryInterface $menuRepository,
        private RestaurantRepositoryInterface $restaurantRepository
    ) {
    }

    public function execute(string $menuItemId): MenuItem
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
        $currentItem = $this->menuItemRepository->findById($menuItemId);
        if (!$currentItem) {
            throw new \Exception("Menu item not found.");
        }

        // 4. Vérifier la chaîne de propriété
        $category = $this->menuCategoryRepository->findById($currentItem->getCategoryId());
        if (!$category) {
            throw new \Exception("Menu category not found.");
        }

        $menu = $this->menuRepository->findById($category->getMenuId());
        if (!$menu) {
            throw new \Exception("Menu not found.");
        }

        // 5. Vérifier les permissions
        if (!in_array($user->role, ['admin', 'franchise_manager'])) {
            $restaurant = $this->restaurantRepository->findByOwnerId($user->id);
            if (!$restaurant || $restaurant->getId() !== $menu->getRestaurantId()) {
                throw new UnauthorizedException("You do not have permission to update this menu item.");
            }
        }

        // 6. Trouver l'item en-dessous (sort_order plus grand)
        $nextItem = $this->menuItemRepository->findNextItemInCategory(
            $currentItem->getCategoryId(),
            $currentItem->getSortOrder()
        );

        if (!$nextItem) {
            throw new \Exception("Item is already at the bottom.");
        }

        // 7. Échanger les positions
        $currentSortOrder = $currentItem->getSortOrder();
        $nextSortOrder = $nextItem->getSortOrder();

        $currentItem->setSortOrder($nextSortOrder);
        $nextItem->setSortOrder($currentSortOrder);

        // 8. Sauvegarder les changements
        $this->menuItemRepository->update($nextItem);
        return $this->menuItemRepository->update($currentItem);
    }
} 