<?php

namespace App\Http\Controllers\MenuCategory;

use App\Application\Usecases\MenuCategory\UpdateMenuCategoryUsecase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateMenuCategoryController extends Controller
{
    public function __construct(
        private UpdateMenuCategoryUsecase $updateMenuCategoryUsecase
    ) {
        // Le middleware 'auth:sanctum' sera mis sur la route
    }

    public function __invoke(string $id, Request $request)
    {
        $data = $request->validate([
            'name'        => 'sometimes|array',
            'description' => 'nullable|array',
            'sort_order'  => 'nullable|integer',
        ]);

        $menuCategory = $this->updateMenuCategoryUsecase->execute($id, $data);

        return response()->json([
            'message' => 'MenuCategories updated successfully',
            'data'    => [
                'id'          => $menuCategory->getId(),
                'menu_id'     => $menuCategory->getMenuId(),
                'name'        => $menuCategory->getName(),
                'description' => $menuCategory->getDescription(),
                'sort_order'  => $menuCategory->getSortOrder(),
            ]
        ]);
    }
}
