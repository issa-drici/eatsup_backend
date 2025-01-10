<?php

namespace App\Application\Usecases\QrCode;

use App\Domain\Entities\QrCode;
use App\Domain\Repositories\QrCodeRepositoryInterface;

class FindQrCodeByIdUsecase
{
    public function __construct(
        private QrCodeRepositoryInterface $qrCodeRepository
    ) {
    }

    public function execute(string $qrCodeId): QrCode
    {
        $qrCode = $this->qrCodeRepository->findById($qrCodeId);

        if (!$qrCode) {
            throw new \Exception("QR code not found.");
        }

        return $qrCode;
    }
} 