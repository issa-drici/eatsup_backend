<?php

namespace App\Http\Controllers\MenuItem;

use App\Application\Usecases\MenuItem\CountMenuItemsByMenuIdUsecase;
use App\Http\Controllers\Controller;

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
