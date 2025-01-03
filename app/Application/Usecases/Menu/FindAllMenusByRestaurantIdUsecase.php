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
        //
    }

    public function execute(string $restaurantId): array
    {
        // 1. Vérifier l’authentification
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }

        // 2. Vérifier si l'utilisateur a accès au restaurant (selon son rôle)
        if (!in_array($user->role, ['admin', 'franchise_manager'])) {
            $restaurant = $this->restaurantRepository->findByOwnerId($user->id);
            if (!$restaurant || $restaurant->getId() !== $restaurantId) {
                throw new UnauthorizedException("You do not have access to this restaurant.");
            }
        }

        // 3. Récupérer tous les menus liés à ce restaurant
        $menus = $this->menuRepository->findByRestaurantId($restaurant->getId());

        // 4. Retourner les menus sous forme d'un tableau prêt à être renvoyé au client
        return array_map(function ($menu) {
            return [
                'id'       => $menu->getId(),
                'name'     => $menu->getName(),
                'status'   => $menu->getStatus(),
                'banner'   => $menu->getBanner(),
            ];
        }, $menus);
    }
}
