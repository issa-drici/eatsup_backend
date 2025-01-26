<?php

namespace App\Http\Controllers\QrCodeSession;

use App\Application\Usecases\QrCodeSession\CountQrCodeSessionsByRestaurantIdUsecase;
use App\Http\Controllers\Controller;

class CountQrCodeSessionsByRestaurantIdController extends Controller
{
    public function __construct(
        private CountQrCodeSessionsByRestaurantIdUsecase $countQrCodeSessionsByRestaurantIdUsecase
    ) {}

    public function __invoke(string $restaurantId)
    {
        $count = $this->countQrCodeSessionsByRestaurantIdUsecase->execute($restaurantId);

        return response()->json([
            'message' => 'QR code sessions count retrieved successfully',
            'data' => [
                'count' => $count
            ]
        ]);
    }
}
