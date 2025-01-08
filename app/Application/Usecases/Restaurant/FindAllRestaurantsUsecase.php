<?php

namespace App\Application\Usecases\Restaurant;

use Illuminate\Support\Facades\Auth;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Exceptions\UnauthorizedException;

class FindAllRestaurantsUsecase
{
    public function __construct(
        private RestaurantRepositoryInterface $restaurantRepository
    ) {
    }

    public function execute(array $filters = [], int $page = 1, int $perPage = 10): array
    {
        // 1. Vérifier l'authentification
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }

        // 2. Vérifier le rôle (seul l'admin peut voir tous les restaurants)
        if ($user->role !== 'admin') {
            throw new UnauthorizedException("Only administrators can access this resource.");
        }

        // 3. Nettoyer les filtres vides
        $filters = array_filter($filters, fn($value) => !is_null($value) && $value !== '');

        // 4. Récupérer tous les restaurants avec leurs propriétaires
        return $this->restaurantRepository->findAllWithOwnersPaginated($filters, $page, $perPage);
    }
} 