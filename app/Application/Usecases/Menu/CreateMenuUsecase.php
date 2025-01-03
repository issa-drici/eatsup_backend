<?php

namespace App\Application\Usecases\Menu;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Domain\Entities\Menu;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Exceptions\UnauthorizedException;

class CreateMenuUsecase
{
    public function __construct(
        private MenuRepositoryInterface $menuRepository
    ) {
        //
    }

    public function execute(array $data): Menu
    {
        // 1. Vérifier l’auth
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }

        // // 2. Vérifier le rôle
        // if (!in_array($user->role, ['admin', 'restaurant_owner', 'franchise_manager'])) {
        //     throw new UnauthorizedException("User not allowed to create restaurant.");
        // }


        // 3. Créer l’entité
        $menu = new Menu(
            id: Str::uuid()->toString(),
            restaurantId: $user->restaurant->id,
            name: ['fr' => 'Menu 1'],
            status: 'published',
        );

        // 4. Persister
        return $this->menuRepository->create($menu);
    }
}
