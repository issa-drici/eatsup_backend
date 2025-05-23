<?php

namespace App\Application\Usecases\MenuCategory;

use Illuminate\Support\Facades\Auth;

use App\Domain\Entities\MenuCategory;
use App\Domain\Repositories\MenuCategoryRepositoryInterface;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Exceptions\UnauthorizedException;

class UpdateMenuCategoryUsecase
{
    public function __construct(
        private MenuCategoryRepositoryInterface $menuCategoryRepository,
        private MenuRepositoryInterface $menuRepository,
        private RestaurantRepositoryInterface $restaurantRepository
    ) {
    }

    public function execute(string $menuCategoryId, array $data): MenuCategory
    {
        // 1. Auth
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }

        // 2. Rôle
        if (!in_array($user->role, ['admin', 'restaurant_owner', 'franchise_manager'])) {
            throw new UnauthorizedException("User not allowed to update categories.");
        }

        // 3. Retrouver la cat
        $existingCategory = $this->menuCategoryRepository->findById($menuCategoryId);
        if (!$existingCategory) {
            throw new \Exception("Menu category not found.");
        }

        // 4. Vérifier le menu => si c'est bien le sien
        $menuId = $existingCategory->getMenuId();
        $menu = $this->menuRepository->findById($menuId);
        if (!$menu) {
            throw new \Exception("Menu not found for this category.");
        }

        // 5. Si l'utilisateur n'est pas admin ou franchise_manager, vérifier qu'il est propriétaire
        if (!in_array($user->role, ['admin', 'franchise_manager'])) {
            $restaurant = $this->restaurantRepository->findByOwnerId($user->id);
            if (!$restaurant || $restaurant->getId() !== $menu->getRestaurantId()) {
                throw new UnauthorizedException("You do not have permission to update this menu category.");
            }
        }

        // 6. Mettre à jour l'entité
        if (isset($data['name'])) {
            $existingCategory->setName($data['name']);
        }
        if (array_key_exists('description', $data)) {
            $existingCategory->setDescription($data['description']);
        }
        if (isset($data['sort_order'])) {
            $existingCategory->setSortOrder($data['sort_order']);
        }

        // 7. Sauvegarde
        return $this->menuCategoryRepository->update($existingCategory);
    }
}
