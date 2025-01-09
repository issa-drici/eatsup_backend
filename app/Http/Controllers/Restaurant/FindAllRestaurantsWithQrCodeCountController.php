<?php

namespace App\Http\Controllers\Restaurant;

use App\Application\Usecases\Restaurant\FindAllRestaurantsWithQrCodeCountUsecase;
use App\Http\Controllers\Controller;

class FindAllRestaurantsWithQrCodeCountController extends Controller
{
    public function __construct(
        private FindAllRestaurantsWithQrCodeCountUsecase $findAllRestaurantsWithQrCodeCountUsecase
    ) {
    }

    public function __invoke()
    {
        $restaurants = $this->findAllRestaurantsWithQrCodeCountUsecase->execute();

        return response()->json([
            'message' => 'Restaurants with QR code count retrieved successfully',
            'data'    => $restaurants
        ]);
    }
} 