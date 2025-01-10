<?php

namespace App\Http\Controllers\QrCode;

use App\Application\Usecases\QrCode\FindQrCodeByIdUsecase;
use App\Http\Controllers\Controller;

class FindQrCodeByIdController extends Controller
{
    public function __construct(
        private FindQrCodeByIdUsecase $findQrCodeByIdUsecase
    ) {
    }

    public function __invoke(string $qrCodeId)
    {
        $qrCode = $this->findQrCodeByIdUsecase->execute($qrCodeId);

        return response()->json([
            'message' => 'QR code retrieved successfully',
            'data'    => [
                'id' => $qrCode->getId(),
                'restaurant_id' => $qrCode->getRestaurantId(),
                'menu_id' => $qrCode->getMenuId(),
                'qr_type' => $qrCode->getQrType(),
                'label' => $qrCode->getLabel(),
                'status' => $qrCode->getStatus()
            ]
        ]);
    }
} 