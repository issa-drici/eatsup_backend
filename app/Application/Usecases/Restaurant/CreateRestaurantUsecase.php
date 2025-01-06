<?php

namespace App\Application\Usecases\Restaurant;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Domain\Entities\Restaurant;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Exceptions\UnauthorizedException;

class CreateRestaurantUsecase
{
    public function __construct(
        private RestaurantRepositoryInterface $restaurantRepository
    ) {
        //
    }

    public function execute(array $data): Restaurant
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
        $restaurant = new Restaurant(
            id: Str::uuid()->toString(),
            ownerId: $user->id,
            name: $user->name,
        );

        // 4. Persister
        return $this->restaurantRepository->create($restaurant);
    }
}
