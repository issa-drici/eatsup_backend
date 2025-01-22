<?php

namespace App\Http\Controllers\WebsiteSession;

use App\Application\Usecases\WebsiteSession\CountWebsiteSessionsByRestaurantIdUsecase;
use App\Http\Controllers\Controller;

class CountWebsiteSessionsByRestaurantIdController extends Controller
{
    public function __construct(
        private CountWebsiteSessionsByRestaurantIdUsecase $countWebsiteSessionsByRestaurantIdUsecase
    ) {
    }

    public function __invoke(string $restaurantId)
    {
        $count = $this->countWebsiteSessionsByRestaurantIdUsecase->execute($restaurantId);

        return response()->json([
            'message' => 'Website sessions count retrieved successfully',
            'data' => [
                'count' => $count
            ]
        ]);
    }
} 