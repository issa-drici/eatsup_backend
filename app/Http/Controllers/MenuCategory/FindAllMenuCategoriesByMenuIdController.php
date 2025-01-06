<?php

namespace App\Http\Controllers\MenuCategory;

use App\Application\Usecases\MenuCategory\FindAllMenuCategoriesByMenuIdUsecase;
use App\Http\Controllers\Controller;

class FindAllMenuCategoriesByMenuIdController extends Controller
{
    public function __construct(
        private FindAllMenuCategoriesByMenuIdUsecase $findAllMenuCategoriesByMenuIdUsecase
    ) {
    }

    public function __invoke(string $menuId)
    {
        $categories = $this->findAllMenuCategoriesByMenuIdUsecase->execute($menuId);

        return response()->json([
            'message' => 'Menu categories retrieved successfully',
            'data'    => $categories
        ]);
    }
} 