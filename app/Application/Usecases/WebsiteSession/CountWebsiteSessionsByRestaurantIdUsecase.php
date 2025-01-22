<?php

namespace App\Application\Usecases\WebsiteSession;

use Illuminate\Support\Facades\Auth;
use App\Domain\Repositories\WebsiteSessionRepositoryInterface;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Exceptions\UnauthorizedException;

class CountWebsiteSessionsByRestaurantIdUsecase
{
    public function __construct(
        private WebsiteSessionRepositoryInterface $websiteSessionRepository,
        private RestaurantRepositoryInterface $restaurantRepository
    ) {
    }

    public function execute(string $restaurantId): int
    {
        // 1. Vérifier l'authentification
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }

        // 2. Vérifier les permissions selon le rôle
        if (!in_array($user->role, ['admin', 'restaurant_owner', 'franchise_manager'])) {
            throw new UnauthorizedException("You do not have permission to view website sessions.");
        }

        // 3. Si l'utilisateur n'est pas admin ou franchise_manager, vérifier qu'il est propriétaire
        if (!in_array($user->role, ['admin', 'franchise_manager'])) {
            $restaurant = $this->restaurantRepository->findByOwnerId($user->id);
            if (!$restaurant || $restaurant->getId() !== $restaurantId) {
                throw new UnauthorizedException("You do not have access to this restaurant's website sessions.");
            }
        }

        // 4. Compter les sessions
        return $this->websiteSessionRepository->countByRestaurantId($restaurantId);
    }
} 