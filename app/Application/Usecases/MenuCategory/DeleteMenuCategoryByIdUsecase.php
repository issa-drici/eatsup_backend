<?php

namespace App\Application\Usecases\MenuCategory;

use Illuminate\Support\Facades\Auth;
use App\Domain\Repositories\MenuCategoryRepositoryInterface;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Domain\Repositories\MenuItemRepositoryInterface;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Exceptions\UnauthorizedException;

class DeleteMenuCategoryByIdUsecase
{
    public function __construct(
        private MenuCategoryRepositoryInterface $menuCategoryRepository,
        private MenuRepositoryInterface $menuRepository,
        private MenuItemRepositoryInterface $menuItemRepository,
        private RestaurantRepositoryInterface $restaurantRepository
    ) {
    }

    public function execute(string $menuCategoryId): void
    {
        // 1. Vérifier l'authentification
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }

        // 2. Récupérer la catégorie
        $category = $this->menuCategoryRepository->findById($menuCategoryId);
        if (!$category) {
            throw new \Exception("Menu category not found.");
        }

        // 3. Vérifier s'il existe des items dans cette catégorie
        $itemCount = $this->menuItemRepository->countByCategoryId($menuCategoryId);
        if ($itemCount > 0) {
            throw new \Exception("Cannot delete category that contains menu items.");
        }

        // 4. Si l'utilisateur n'est pas admin, vérifier qu'il est propriétaire
        if ($user->role !== 'admin') {
            // Récupérer le menu
            $menu = $this->menuRepository->findById($category->getMenuId());
            if (!$menu) {
                throw new \Exception("Menu not found.");
            }

            // Vérifier que le restaurant appartient à l'utilisateur
            $restaurant = $this->restaurantRepository->findByOwnerId($user->id);
            if (!$restaurant || $restaurant->getId() !== $menu->getRestaurantId()) {
                throw new UnauthorizedException("You do not have permission to delete this menu category.");
            }
        }

        // 5. Supprimer la catégorie
        $this->menuCategoryRepository->delete($menuCategoryId);
    }
} 