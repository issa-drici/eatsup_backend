<?php

namespace App\Application\Usecases\QrCode;

use App\Domain\Entities\QrCode;
use Illuminate\Support\Facades\Auth;
use App\Domain\Repositories\QrCodeRepositoryInterface;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Exceptions\UnauthorizedException;

class UpdateQrCodeUsecase
{
    public function __construct(
        private QrCodeRepositoryInterface $qrCodeRepository,
        private MenuRepositoryInterface $menuRepository
    ) {}

    public function execute(string $qrCodeId, array $data): QrCode
    {

        // 1. Vérifier l'authentification
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }

        // 2. Vérifier les permissions selon le rôle
        if (!in_array($user->role, ['admin'])) {
            throw new UnauthorizedException("You do not have permission to update QR codes.");
        }

        // 3. Récupérer le QR code existant
        $qrCode = $this->qrCodeRepository->findById($qrCodeId);
        if (!$qrCode) {
            throw new \Exception("QR code not found.");
        }
        // 4. Récupérer le premier menu du restaurant
        $menus = $this->menuRepository->findByRestaurantId($data['restaurant_id']);
        if (empty($menus)) {
            throw new \Exception("No menu found for this restaurant.");
        }
        $firstMenu = $menus[0];

        // 5. Mettre à jour l'entité
        $qrCode->setRestaurantId($data['restaurant_id']);
        $qrCode->setMenuId($firstMenu->getId());
        $qrCode->setQrType($data['qr_type']);
        $qrCode->setLabel($data['label'] ?? null);
        $qrCode->setStatus('assigned');

        // 6. Persister
        return $this->qrCodeRepository->update($qrCode);
    }
}
