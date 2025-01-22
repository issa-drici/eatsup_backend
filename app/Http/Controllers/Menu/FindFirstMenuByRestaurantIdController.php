<?php

namespace App\Http\Controllers\Menu;

use App\Http\Controllers\Controller;
use App\Application\Usecases\Menu\FindFirstMenuByRestaurantIdUsecase;

class FindFirstMenuByRestaurantIdController extends Controller
{
    public function __construct(
        private FindFirstMenuByRestaurantIdUsecase $findFirstMenuByRestaurantIdUsecase
    ) {}

    public function __invoke(string $restaurantId)
    {
        $menu = $this->findFirstMenuByRestaurantIdUsecase->execute($restaurantId);

        return response()->json([
            'data' => [
                'id' => $menu->getId(),
                'name' => $menu->getName(),
                'status' => $menu->getStatus(),
                'banners' => $menu->getBanners(),
            ]
        ]);
    }
} 