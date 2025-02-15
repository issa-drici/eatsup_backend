<?php

namespace App\Http\Controllers\Restaurant;

use App\Application\Usecases\Restaurant\FindMenuInfosHomeByRestaurantIdUsecase;
use App\Http\Controllers\Controller;

class FindMenuInfosHomeByRestaurantIdController extends Controller
{
    public function __construct(
        private FindMenuInfosHomeByRestaurantIdUsecase $findMenuInfosHomeByRestaurantIdUsecase
    ) {
    }

    public function __invoke(string $restaurantId)
    {
        $stats = $this->findMenuInfosHomeByRestaurantIdUsecase->execute($restaurantId);

        return response()->json([
            'message' => 'Restaurant menu infos home retrieved successfully',
            'data' => $stats
        ]);
    }
}
