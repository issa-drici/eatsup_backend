<?php

namespace App\Http\Controllers\MenuItem;

use App\Application\Usecases\MenuItem\CreateMenuItemUsecase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CreateMenuItemController extends Controller
{
    public function __construct(
        private CreateMenuItemUsecase $createMenuItemUsecase
    ) {
    }

    public function __invoke(string $menuCategoryId, Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|array',
            'description' => 'nullable|array',
            'price'       => 'required|numeric|min:0',
            'allergens'   => 'nullable|string',
            'images'      => 'nullable|array',
            'is_active'   => 'boolean',
        ]);

        // Ajouter le menuCategoryId aux données
        $data['category_id'] = $menuCategoryId;

        $menuItem = $this->createMenuItemUsecase->execute($data);

        return response()->json([
            'message' => 'Menu item created successfully',
            'data'    => [
                'id'           => $menuItem->getId(),
                'category_id'  => $menuItem->getCategoryId(),
                'name'        => $menuItem->getName(),
                'description' => $menuItem->getDescription(),
                'price'       => $menuItem->getPrice(),
                'allergens'   => $menuItem->getAllergens(),
                'images'      => $menuItem->getImages(),
                'is_active'   => $menuItem->isActive(),
                'sort_order'  => $menuItem->getSortOrder(),
            ]
        ], 201);
    }
} 