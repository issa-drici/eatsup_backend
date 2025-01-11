<?php

namespace App\Application\Usecases\MenuItem;

use Illuminate\Support\Facades\Auth;
use App\Domain\Repositories\MenuItemRepositoryInterface;
use App\Domain\Repositories\MenuCategoryRepositoryInterface;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Exceptions\UnauthorizedException;

class DeleteMenuItemByIdUsecase
{
    public function __construct(
        private MenuItemRepositoryInterface $menuItemRepository,
        private MenuCategoryRepositoryInterface $menuCategoryRepository,
        private MenuRepositoryInterface $menuRepository,
        private RestaurantRepositoryInterface $restaurantRepository
    ) {
    }

    public function execute(string $menuItemId): void
    {
        // 1. Vérifier l'authentification
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }

        // 2. Récupérer l'item
        $menuItem = $this->menuItemRepository->findById($menuItemId);
        if (!$menuItem) {
            throw new \Exception("Menu item not found.");
        }

        // 3. Si l'utilisateur n'est pas admin, vérifier qu'il est propriétaire
        if ($user->role !== 'admin') {
            // Récupérer la catégorie
            $category = $this->menuCategoryRepository->findById($menuItem->getCategoryId());
            if (!$category) {
                throw new \Exception("Menu category not found.");
            }

            // Récupérer le menu
            $menu = $this->menuRepository->findById($category->getMenuId());
            if (!$menu) {
                throw new \Exception("Menu not found.");
            }

            // Vérifier que le restaurant appartient à l'utilisateur
            $restaurant = $this->restaurantRepository->findByOwnerId($user->id);
            if (!$restaurant || $restaurant->getId() !== $menu->getRestaurantId()) {
                throw new UnauthorizedException("You do not have permission to delete this menu item.");
            }
        }

        // 4. Supprimer l'item
        $this->menuItemRepository->delete($menuItemId);
    }
} 