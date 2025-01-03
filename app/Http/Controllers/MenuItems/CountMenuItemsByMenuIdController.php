<?php

namespace App\Http\Controllers\MenuItems;

use App\Application\Usecases\MenuItems\CountMenuItemsByMenuIdUsecase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CountMenuItemsByMenuIdController extends Controller
{
    private CountMenuItemsByMenuIdUsecase $countMenuItemsByMenuIdUsecase;

    public function __construct(CountMenuItemsByMenuIdUsecase $countMenuItemsByMenuIdUsecase)
    {
        $this->countMenuItemsByMenuIdUsecase = $countMenuItemsByMenuIdUsecase;
    }

    public function __invoke(string $menuId)
    {
        $count = $this->countMenuItemsByMenuIdUsecase->execute($menuId);

        return response()->json([
            'message' => 'Menu items count retrieved successfully',
            'data'    => [
                'menu_id' => $menuId,
                'count'   => $count,
            ],
        ], 200);
    }
}
