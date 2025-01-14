<?php

namespace App\Http\Controllers\MenuCategory;

use App\Application\Usecases\MenuCategory\UpdateMenuCategoryMoveDownUsecase;
use App\Http\Controllers\Controller;

class UpdateMenuCategoryMoveDownController extends Controller
{
    public function __construct(
        private UpdateMenuCategoryMoveDownUsecase $updateMenuCategoryMoveDownUsecase
    ) {
    }

    public function __invoke(string $menuCategoryId)
    {
        $menuCategory = $this->updateMenuCategoryMoveDownUsecase->execute($menuCategoryId);

        return response()->json([
            'message' => 'Menu category moved down successfully',
            'data' => [
                'id' => $menuCategory->getId(),
                'menu_id' => $menuCategory->getMenuId(),
                'name' => $menuCategory->getName(),
                'sort_order' => $menuCategory->getSortOrder(),
            ]
        ]);
    }
}