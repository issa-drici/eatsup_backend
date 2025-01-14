<?php

namespace App\Http\Controllers\MenuCategory;

use App\Application\Usecases\MenuCategory\UpdateMenuCategoryMoveUpUsecase;
use App\Http\Controllers\Controller;

class UpdateMenuCategoryMoveUpController extends Controller
{
    public function __construct(
        private UpdateMenuCategoryMoveUpUsecase $updateMenuCategoryMoveUpUsecase
    ) {
    }

    public function __invoke(string $menuCategoryId)
    {
        $menuCategory = $this->updateMenuCategoryMoveUpUsecase->execute($menuCategoryId);

        return response()->json([
            'message' => 'Menu category moved up successfully',
            'data' => [
                'id' => $menuCategory->getId(),
                'menu_id' => $menuCategory->getMenuId(),
                'name' => $menuCategory->getName(),
                'sort_order' => $menuCategory->getSortOrder(),
            ]
        ]);
    }
} 