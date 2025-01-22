<?php

namespace App\Http\Controllers\Menu;

use App\Application\Usecases\Menu\FindMenuByIdUsecase;
use App\Http\Controllers\Controller;

class FindMenuByIdController extends Controller
{
    public function __construct(
        private FindMenuByIdUsecase $findMenuByIdUsecase
    ) {
    }

    public function __invoke(string $menuId)
    {
        $menu = $this->findMenuByIdUsecase->execute($menuId);

        return response()->json([
            'message' => 'Menu retrieved successfully',
            'data'    => [
                'id'       => $menu->getId(),
                'restaurant_id' => $menu->getRestaurantId(),
                'name'     => $menu->getName(),
                'status'   => $menu->getStatus(),
                'banners'   => $menu->getBanners(),
            ]
        ]);
    }
} 