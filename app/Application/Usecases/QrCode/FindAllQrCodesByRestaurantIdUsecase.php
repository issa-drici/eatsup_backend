<?php

namespace App\Application\Usecases\QrCode;

use Illuminate\Support\Facades\Auth;
use App\Domain\Repositories\QrCodeRepositoryInterface;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Exceptions\UnauthorizedException;

class FindAllQrCodesByRestaurantIdUsecase
{
    public function __construct(
        private QrCodeRepositoryInterface $qrCodeRepository,
        private RestaurantRepositoryInterface $restaurantRepository
    ) {
    }

    public function execute(string $restaurantId): array
    {
        // 1. Vérifier l'authentification
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }

        // 2. Vérifier les permissions selon le rôle
        if (!in_array($user->role, ['admin', 'restaurant_owner'])) {
            throw new UnauthorizedException("You do not have permission to view QR codes.");
        }

        // 3. Si l'utilisateur est restaurant_owner, vérifier qu'il est propriétaire
        if ($user->role === 'restaurant_owner') {
            $restaurant = $this->restaurantRepository->findByOwnerId($user->id);
            if (!$restaurant || $restaurant->getId() !== $restaurantId) {
                throw new UnauthorizedException("You do not have access to this restaurant's QR codes.");
            }
        }

        // 4. Récupérer tous les QR codes du restaurant
        $qrCodes = $this->qrCodeRepository->findAllByRestaurantId($restaurantId);

        // 5. Formater les données pour la réponse
        return array_map(function ($qrCode) {
            return [
                'id' => $qrCode->getId(),
                'restaurant_id' => $qrCode->getRestaurantId(),
                'menu_id' => $qrCode->getMenuId(),
                'qr_type' => $qrCode->getQrType(),
                'label' => $qrCode->getLabel(),
                'status' => $qrCode->getStatus()
            ];
        }, $qrCodes);
    }
} 