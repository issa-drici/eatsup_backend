<?php

namespace App\Application\Usecases\QrCodeSession;

use Illuminate\Support\Str;
use App\Domain\Entities\QrCodeSession;
use App\Domain\Repositories\QrCodeSessionRepositoryInterface;
use App\Domain\Repositories\QrCodeRepositoryInterface;

class CreateQrCodeSessionUsecase
{
    public function __construct(
        private QrCodeSessionRepositoryInterface $qrCodeSessionRepository,
        private QrCodeRepositoryInterface $qrCodeRepository
    ) {
    }

    public function execute(array $data): QrCodeSession
    {
        // 1. Vérifier si le QR code existe
        $qrCode = $this->qrCodeRepository->findById($data['qr_code_id']);
        if (!$qrCode) {
            throw new \Exception("QR code not found.");
        }

        // 2. Vérifier s'il existe une session récente
        $recentSession = $this->qrCodeSessionRepository->findRecentByAttributes(
            qrCodeId: $data['qr_code_id'],
            ipAddress: $data['ip_address'],
            userAgent: $data['user_agent'],
            minutes: 2
        );

        // Si une session récente existe, la retourner
        if ($recentSession) {
            return $recentSession;
        }

        // 3. Créer l'entité
        $session = new QrCodeSession(
            id: Str::uuid()->toString(),
            qrCodeId: $data['qr_code_id'],
            scannedAt: now()->format('Y-m-d H:i:s'),
            ipAddress: $data['ip_address'],
            userAgent: $data['user_agent'],
            location: $data['location'] ?? null,
            createdAt: now()->format('Y-m-d H:i:s'),
            updatedAt: now()->format('Y-m-d H:i:s')
        );

        // 4. Persister
        return $this->qrCodeSessionRepository->create($session);
    }
} 