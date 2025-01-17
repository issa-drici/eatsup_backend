<?php

namespace App\Http\Controllers\Website;

use App\Application\Usecases\Website\FindWebsiteByRestaurantIdUsecase;
use App\Http\Controllers\Controller;

class FindWebsiteByRestaurantIdController extends Controller
{
    public function __construct(
        private FindWebsiteByRestaurantIdUsecase $findWebsiteByRestaurantIdUsecase
    ) {
    }

    public function __invoke(string $restaurantId)
    {
        $websiteDTO = $this->findWebsiteByRestaurantIdUsecase->execute($restaurantId);

        return response()->json([
            'data' => $websiteDTO->toArray()
        ]);
    }
} 