<?php

namespace App\Http\Controllers\Menu;

use App\Application\Usecases\Menu\FindAllMenusByRestaurantIdUsecase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FindAllMenusByRestaurantIdController extends Controller
{
    private FindAllMenusByRestaurantIdUsecase $findAllMenusByRestaurantIdUsecase;

    public function __construct(FindAllMenusByRestaurantIdUsecase $findAllMenusByRestaurantIdUsecase)
    {
        $this->findAllMenusByRestaurantIdUsecase = $findAllMenusByRestaurantIdUsecase;
    }

    public function __invoke(string $restaurantId, Request $request)
    {
        $menus = $this->findAllMenusByRestaurantIdUsecase->execute($restaurantId);

        return response()->json([
            'message' => 'Menus retrieved successfully',
            'data'    => $menus,
        ], 200);
    }
}
