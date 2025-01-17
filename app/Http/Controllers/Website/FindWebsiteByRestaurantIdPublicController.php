<?php

namespace App\Http\Controllers\Website;

use App\Application\Usecases\Website\FindWebsiteByRestaurantIdPublicUsecase;
use App\Http\Controllers\Controller;

class FindWebsiteByRestaurantIdPublicController extends Controller
{
    public function __construct(
        private FindWebsiteByRestaurantIdPublicUsecase $findWebsiteByRestaurantIdPublicUsecase
    ) {
    }

    public function __invoke(string $restaurantId)
    {
        $websiteDTO = $this->findWebsiteByRestaurantIdPublicUsecase->execute($restaurantId);

        return response()->json([
            'data' => $websiteDTO->toArray()
        ]);
    }
} 