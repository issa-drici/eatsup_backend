<?php

namespace App\Http\Controllers\MenuCategory;

use App\Application\Usecases\MenuCategory\FindMenuCategoryByIdUsecase;
use App\Http\Controllers\Controller;

class FindMenuCategoryByIdController extends Controller
{
    public function __construct(
        private FindMenuCategoryByIdUsecase $findMenuCategoryByIdUsecase
    ) {
    }

    public function __invoke(string $menuCategoryId)
    {
        $category = $this->findMenuCategoryByIdUsecase->execute($menuCategoryId);

        return response()->json([
            'message' => 'Menu category retrieved successfully',
            'data'    => [
                'id'          => $category->getId(),
                'menu_id'     => $category->getMenuId(),
                'name'        => $category->getName(),
                'description' => $category->getDescription(),
                'sort_order'  => $category->getSortOrder(),
            ]
        ]);
    }
} 