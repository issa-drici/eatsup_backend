<?php

namespace App\Http\Controllers\Restaurant;

use App\Application\Usecases\Restaurant\FindRestaurantByIdUsecase;
use App\Http\Controllers\Controller;

class FindRestaurantByIdController extends Controller
{
    public function __construct(
        private FindRestaurantByIdUsecase $findRestaurantByIdUsecase
    ) {
    }

    public function __invoke(string $restaurantId)
    {
        $restaurant = $this->findRestaurantByIdUsecase->execute($restaurantId);

        return response()->json([
            'message' => 'Restaurant retrieved successfully',
            'data'    => $restaurant
        ]);
    }
} 