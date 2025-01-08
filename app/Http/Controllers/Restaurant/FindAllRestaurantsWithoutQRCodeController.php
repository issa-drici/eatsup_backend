<?php

namespace App\Http\Controllers\Restaurant;

use App\Application\Usecases\Restaurant\FindAllRestaurantsWithoutQRCodeUsecase;
use App\Http\Controllers\Controller;

class FindAllRestaurantsWithoutQRCodeController extends Controller
{
    public function __construct(
        private FindAllRestaurantsWithoutQRCodeUsecase $findAllRestaurantsWithoutQRCodeUsecase
    ) {
    }

    public function __invoke()
    {
        $restaurants = $this->findAllRestaurantsWithoutQRCodeUsecase->execute();

        return response()->json([
            'message' => 'Restaurants without QR code retrieved successfully',
            'data'    => $restaurants
        ]);
    }
} 