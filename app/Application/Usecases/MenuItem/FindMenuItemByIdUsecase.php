<?php

namespace App\Application\Usecases\MenuItem;

use Illuminate\Support\Facades\Auth;
use App\Domain\Entities\MenuItem;
use App\Domain\Repositories\MenuItemRepositoryInterface;
use App\Domain\Repositories\MenuCategoryRepositoryInterface;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Exceptions\UnauthorizedException;

class FindMenuItemByIdUsecase
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

        // 2. Retrouver l'item
        $menuItem = $this->menuItemRepository->findById($menuItemId);
        if (!$menuItem) {
            throw new \Exception("Menu item not found.");
        }

        // 3. Vérifier la chaîne de propriété (item -> category -> menu -> restaurant)
        $category = $this->menuCategoryRepository->findById($menuItem->getCategoryId());
        if (!$category) {
            throw new \Exception("Menu category not found.");
        }

        $menu = $this->menuRepository->findById($category->getMenuId());
        if (!$menu) {
            throw new \Exception("Menu not found.");
        }

        // 4. Si l'utilisateur n'est pas admin ou franchise_manager, vérifier qu'il est propriétaire
        if (!in_array($user->role, ['admin', 'franchise_manager'])) {
            $restaurant = $this->restaurantRepository->findByOwnerId($user->id);
            if (!$restaurant || $restaurant->getId() !== $menu->getRestaurantId()) {
                throw new UnauthorizedException("You do not have permission to view this menu item.");
            }
        }

        return $menuItem;
    }
}