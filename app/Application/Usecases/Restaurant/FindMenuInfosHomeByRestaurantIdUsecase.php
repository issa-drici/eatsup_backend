<?php

namespace App\Application\Usecases\Restaurant;

use Illuminate\Support\Facades\Auth;
use App\Domain\Repositories\MenuCategoryRepositoryInterface;
use App\Domain\Repositories\MenuItemRepositoryInterface;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Exceptions\UnauthorizedException;

class FindMenuInfosHomeByRestaurantIdUsecase
{
    public function __construct(
        private MenuCategoryRepositoryInterface $menuCategoryRepository,
        private MenuItemRepositoryInterface $menuItemRepository,
        private MenuRepositoryInterface $menuRepository,
        private RestaurantRepositoryInterface $restaurantRepository
    ) {}

    public function execute(string $restaurantId): array
    {
        // 1. Vérifier l'authentification
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }

        // 2. Vérifier si le restaurant existe
        $restaurant = $this->restaurantRepository->findById($restaurantId);
        if (!$restaurant) {
            throw new \Exception("Restaurant not found.");
        }

        // 3. Si l'utilisateur n'est pas admin ou franchise_manager, vérifier qu'il est propriétaire
        if (!in_array($user->role, ['admin', 'franchise_manager'])) {
            $userRestaurant = $this->restaurantRepository->findByOwnerId($user->id);
            if (!$userRestaurant || $userRestaurant->getId() !== $restaurantId) {
                throw new UnauthorizedException("You do not have access to this restaurant's statistics.");
            }
        }


        // 5. Récupérer les menus du restaurant
        $menus = $this->menuRepository->findByRestaurantId($restaurantId);
        $uniqueMenuId = count($menus) === 1 ? $menus[0]->getId() : null;

        // 4. Récupérer les statistiques et les catégories
        $categories = $this->menuCategoryRepository->findAllByMenuId($uniqueMenuId);
        $categoriesCount = count($categories);
        $itemsCount = $this->menuItemRepository->countByRestaurantId($restaurantId);

        // 6. Récupérer l'ID de la catégorie unique si applicable
        $uniqueCategory = $categoriesCount === 1 ? ['id' => $categories[0]->getId(), 'name' => $categories[0]->getName()] : null;

        return [
            'categories_count' => $categoriesCount,
            'items_count' => $itemsCount,
            'menu_id' => $uniqueMenuId,
            'category' => $uniqueCategory
        ];
    }
}
