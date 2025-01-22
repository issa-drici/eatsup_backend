<?php

namespace App\Application\Usecases\Menu;

use Illuminate\Support\Facades\Auth;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Exceptions\UnauthorizedException;

class FindAllMenusByRestaurantIdUsecase
{
    public function __construct(
        private MenuRepositoryInterface $menuRepository,
        private RestaurantRepositoryInterface $restaurantRepository
    ) {
    }

    public function execute(string $requestedRestaurantId): array
    {
        // 1. Vérifier l'authentification
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }

        $restaurantId = $requestedRestaurantId;

        // 2. Si l'utilisateur n'est pas admin ou franchise_manager
        if (!in_array($user->role, ['admin', 'franchise_manager'])) {
            // Récupérer le restaurant de l'utilisateur
            $userRestaurant = $this->restaurantRepository->findByOwnerId($user->id);
            if (!$userRestaurant) {
                throw new UnauthorizedException("No restaurant found for this user.");
            }

            // Si le restaurant demandé n'est pas celui de l'utilisateur,
            // on utilise celui de l'utilisateur à la place
            if ($userRestaurant->getId() !== $requestedRestaurantId) {
                $restaurantId = $userRestaurant->getId();
            }
        }

        // 3. Récupérer tous les menus liés au restaurant
        $menus = $this->menuRepository->findByRestaurantId($restaurantId);

        // 4. Retourner les menus sous forme d'un tableau
        return array_map(function ($menu) {
            return [
                'id'       => $menu->getId(),
                'name'     => $menu->getName(),
                'status'   => $menu->getStatus(),
                'banners'   => $menu->getBanners(),
            ];
        }, $menus);
    }
}
