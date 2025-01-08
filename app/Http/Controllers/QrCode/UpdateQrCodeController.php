<?php

namespace App\Http\Controllers\QrCode;

use App\Application\Usecases\QrCode\UpdateQrCodeUsecase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateQrCodeController extends Controller
{
    public function __construct(
        private UpdateQrCodeUsecase $updateQrCodeUsecase
    ) {
    }

    public function __invoke(string $qrCodeId, Request $request)
    {
        $data = $request->validate([
            'restaurant_id' => 'required|uuid',
            'qr_type' => 'required|string',
            'label' => 'nullable|string',
        ]);

        $qrCode = $this->updateQrCodeUsecase->execute($qrCodeId, $data);

        return response()->json([
            'message' => 'QR code updated successfully',
            'data' => [
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