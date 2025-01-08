<?php

namespace App\Http\Controllers\QrCode;

use App\Application\Usecases\QrCode\FindAllQrCodesByRestaurantIdUsecase;
use App\Http\Controllers\Controller;

class FindAllQrCodesByRestaurantIdController extends Controller
{
    public function __construct(
        private FindAllQrCodesByRestaurantIdUsecase $findAllQrCodesByRestaurantIdUsecase
    ) {
    }

    public function __invoke(string $restaurantId)
    {
        $qrCodes = $this->findAllQrCodesByRestaurantIdUsecase->execute($restaurantId);

        return response()->json([
            'message' => 'QR codes retrieved successfully',
            'data' => $qrCodes
        ]);
    }
} 