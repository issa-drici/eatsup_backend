<?php

namespace App\Application\Usecases\Restaurant;

use Illuminate\Support\Facades\Auth;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Exceptions\UnauthorizedException;
use App\Application\DTOs\RestaurantWithOwnerDTO;

class FindRestaurantByIdUsecase
{
    public function __construct(
        private RestaurantRepositoryInterface $restaurantRepository
    ) {
    }

    public function execute(string $restaurantId): RestaurantWithOwnerDTO
    {
        // 1. Vérifier l'authentification
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }

        // 2. Vérifier les permissions selon le rôle
        if (!in_array($user->role, ['admin', 'restaurant_owner', 'franchise_manager'])) {
            throw new UnauthorizedException("You do not have access to this restaurant.");
        }
        
        // 3. Si l'utilisateur n'est pas admin, vérifier qu'il est propriétaire
        if ($user->role === 'restaurant_owner') {
            $userRestaurant = $this->restaurantRepository->findByOwnerId($user->id);

            if (!$userRestaurant || $userRestaurant->getId() !== $restaurantId) {
                throw new UnauthorizedException("You do not have access to this restaurant.");
            }
        }

        // 4. Récupérer le restaurant avec les informations du propriétaire
        $restaurantWithOwner = $this->restaurantRepository->findByIdWithOwner($restaurantId);
        if (!$restaurantWithOwner) {
            throw new \Exception("Restaurant not found.");
        }
        
        return $restaurantWithOwner;
    }
} 