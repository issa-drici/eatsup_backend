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
        // Le middleware 'auth:sanctum' sera mis sur la route
    }

    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'menu_id'     => 'required|uuid',
            'name'        => 'required|array',
            'description' => 'nullable|array',
            'sort_order'  => 'nullable|integer',
        ]);

        $menuCategory = $this->createMenuCategoryUsecase->execute($data);

        return response()->json([
            'message' => 'MenuCategory created successfully',
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
