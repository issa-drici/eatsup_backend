<?php

namespace App\Application\Usecases\MenuItem;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Domain\Entities\MenuItem;
use App\Domain\Repositories\MenuItemRepositoryInterface;
use App\Domain\Repositories\MenuCategoryRepositoryInterface;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Exceptions\UnauthorizedException;

class CreateMenuItemUsecase
{
    public function __construct(
        private MenuItemRepositoryInterface $menuItemRepository,
        private MenuCategoryRepositoryInterface $menuCategoryRepository,
        private RestaurantRepositoryInterface $restaurantRepository,
        private MenuRepositoryInterface $menuRepository,

    ) {}

    public function execute(array $data): MenuItem
    {
        // 1. Vérifier l'authentification
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }

        // 2. Vérifier le rôle
        if (!in_array($user->role, ['admin', 'restaurant_owner', 'franchise_manager'])) {
            throw new UnauthorizedException("User not allowed to create menu items.");
        }

        // 3. Récupérer le restaurant lié à cet utilisateur
        $restaurant = $this->restaurantRepository->findByOwnerId($user->id);
        if (!$restaurant) {
            throw new UnauthorizedException("No restaurant found for this user.");
        }

        // 4. Vérifier si la catégorie existe
        $categoryId = $data['category_id'];
        $category = $this->menuCategoryRepository->findById($categoryId);
        if (!$category) {
            throw new \Exception("Menu category not found.");
        }

        // 5. Vérifier que la catégorie appartient au restaurant de l'utilisateur
        $menu = $this->menuRepository->findById($category->getMenuId());
        if (!$menu || $menu->getRestaurantId() !== $restaurant->getId()) {
            throw new UnauthorizedException("This menu category does not belong to your restaurant.");
        }

        // 6. Récupérer le plus grand sort_order pour cette catégorie
        $maxSortOrder = $this->menuItemRepository->getMaxSortOrderByCategoryId($categoryId);
        $nextSortOrder = $maxSortOrder + 1;

        // 7. Créer l'entité
        $menuItem = new MenuItem(
            id: Str::uuid()->toString(),
            categoryId: $categoryId,
            name: $data['name'],
            description: $data['description'] ?? null,
            price: $data['price'],
            allergens: $data['allergens'] ?? null,
            images: $data['images'] ?? null,
            sortOrder: $nextSortOrder,
            isActive: $data['is_active'] ?? true
        );

        // 8. Persister
        return $this->menuItemRepository->create($menuItem);
    }
}
