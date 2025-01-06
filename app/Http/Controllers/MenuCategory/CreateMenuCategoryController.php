<?php

namespace App\Http\Controllers\MenuCategory;

use App\Application\Usecases\MenuCategory\CreateMenuCategoryUsecase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CreateMenuCategoryController extends Controller
{
    public function __construct(
        private CreateMenuCategoryUsecase $createMenuCategoryUsecase
    ) {
    }

    public function __invoke(string $menuId, Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|array',
            'description' => 'nullable|array',
        ]);

        // Ajouter le menuId aux données
        $data['menu_id'] = $menuId;

        $menuCategory = $this->createMenuCategoryUsecase->execute($data);

        return response()->json([
            'message' => 'Menu category created successfully',
            'data'    => [
                'id'          => $menuCategory->getId(),
                'menu_id'     => $menuCategory->getMenuId(),
                'name'        => $menuCategory->getName(),
                'description' => $menuCategory->getDescription(),
                'sort_order'  => $menuCategory->getSortOrder(),
            ]
        ], 201);
    }
}
