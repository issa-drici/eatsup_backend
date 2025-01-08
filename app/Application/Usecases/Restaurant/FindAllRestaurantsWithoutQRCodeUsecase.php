<?php

namespace App\Application\Usecases\Restaurant;

use Illuminate\Support\Facades\Auth;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Exceptions\UnauthorizedException;
use App\Application\DTOs\RestaurantWithOwnerDTO;

class FindAllRestaurantsWithoutQRCodeUsecase
{
    public function __construct(
        private RestaurantRepositoryInterface $restaurantRepository
    ) {
    }

    /**
     * @return RestaurantWithOwnerDTO[]
     */
    public function execute(): array
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

        // 3. Récupérer les restaurants sans QR code
        return $this->restaurantRepository->findAllWithoutQRCode();
    }
} 