<?php

namespace App\Application\Usecases\Menu;

use Illuminate\Support\Facades\Auth;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Exceptions\UnauthorizedException;
use App\Domain\Entities\Menu;

class FindMenuByIdUsecase
{
    public function __construct(
        private MenuRepositoryInterface $menuRepository,
        private RestaurantRepositoryInterface $restaurantRepository
    ) {
    }

    public function execute(string $menuId): Menu
    {
        // 1. Vérifier l'authentification
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }

        // 2. Récupérer le menu
        $menu = $this->menuRepository->findById($menuId);
        if (!$menu) {
            throw new \Exception("Menu not found.");
        }

        // 3. Si l'utilisateur n'est pas admin ou franchise_manager, vérifier qu'il est propriétaire
        if (!in_array($user->role, ['admin', 'franchise_manager'])) {
            $restaurant = $this->restaurantRepository->findByOwnerId($user->id);
            if (!$restaurant || $restaurant->getId() !== $menu->getRestaurantId()) {
                throw new UnauthorizedException("You do not have permission to view this menu.");
            }
        }

        return $menu;
    }
} 