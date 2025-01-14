<?php

namespace App\Application\Usecases\MenuCategory;

use Illuminate\Support\Facades\Auth;
use App\Domain\Entities\MenuCategory;
use App\Domain\Repositories\MenuCategoryRepositoryInterface;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Exceptions\UnauthorizedException;

class UpdateMenuCategoryMoveUpUsecase
{
    public function __construct(
        private MenuCategoryRepositoryInterface $menuCategoryRepository,
        private MenuRepositoryInterface $menuRepository,
        private RestaurantRepositoryInterface $restaurantRepository
    ) {
    }

    public function execute(string $menuCategoryId): MenuCategory
    {
        // 1. Auth
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }

        // 2. Rôle
        if (!in_array($user->role, ['admin', 'restaurant_owner', 'franchise_manager'])) {
            throw new UnauthorizedException("User not allowed to update menu categories.");
        }

        // 3. Retrouver la catégorie
        $currentCategory = $this->menuCategoryRepository->findById($menuCategoryId);
        if (!$currentCategory) {
            throw new \Exception("Menu category not found.");
        }

        // 4. Vérifier le menu
        $menu = $this->menuRepository->findById($currentCategory->getMenuId());
        if (!$menu) {
            throw new \Exception("Menu not found.");
        }

        // 5. Vérifier les permissions
        if (!in_array($user->role, ['admin', 'franchise_manager'])) {
            $restaurant = $this->restaurantRepository->findByOwnerId($user->id);
            if (!$restaurant || $restaurant->getId() !== $menu->getRestaurantId()) {
                throw new UnauthorizedException("You do not have permission to update this menu category.");
            }
        }

        // 6. Trouver la catégorie au-dessus
        $previousCategory = $this->menuCategoryRepository->findPreviousCategoryInMenu(
            $currentCategory->getMenuId(),
            $currentCategory->getSortOrder()
        );

        if (!$previousCategory) {
            throw new \Exception("Category is already at the top.");
        }

        // 7. Échanger les positions
        $currentSortOrder = $currentCategory->getSortOrder();
        $previousSortOrder = $previousCategory->getSortOrder();

        $currentCategory->setSortOrder($previousSortOrder);
        $previousCategory->setSortOrder($currentSortOrder);

        // 8. Sauvegarder les changements
        $this->menuCategoryRepository->update($previousCategory);
        return $this->menuCategoryRepository->update($currentCategory);
    }
} 