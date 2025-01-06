<?php

namespace App\Http\Controllers\MenuItem;

use App\Application\Usecases\MenuItem\FindAllMenuItemsByMenuCategoryIdUsecase;
use App\Http\Controllers\Controller;

class FindAllMenuItemsByMenuCategoryIdController extends Controller
{
    public function __construct(
        private FindAllMenuItemsByMenuCategoryIdUsecase $findAllMenuItemsByMenuCategoryIdUsecase
    ) {
    }

    public function __invoke(string $menuCategoryId)
    {
        $items = $this->findAllMenuItemsByMenuCategoryIdUsecase->execute($menuCategoryId);

        return response()->json([
            'message' => 'Menu items retrieved successfully',
            'data'    => $items
        ]);
    }
} 