<?php

namespace App\Http\Controllers\MenuCategories;

use App\Application\Usecases\MenuCategory\CountMenuCategoriesByMenuIdUsecase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CountMenuCategoriesByMenuIdController extends Controller
{
    private CountMenuCategoriesByMenuIdUsecase $countMenuCategoriesByMenuIdUsecase;

    public function __construct(CountMenuCategoriesByMenuIdUsecase $countMenuCategoriesByMenuIdUsecase)
    {
        $this->countMenuCategoriesByMenuIdUsecase = $countMenuCategoriesByMenuIdUsecase;
    }

    public function __invoke(string $menuId)
    {
        $count = $this->countMenuCategoriesByMenuIdUsecase->execute($menuId);

        return response()->json([
            'message' => 'Menu categories count retrieved successfully',
            'data'    => [
                'menu_id' => $menuId,
                'count'   => $count,
            ],
        ], 200);
    }
}
