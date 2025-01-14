<?php

namespace App\Http\Controllers\MenuItem;

use App\Application\Usecases\MenuItem\UpdateMenuItemUsecase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateMenuItemController extends Controller
{
    public function __construct(
        private UpdateMenuItemUsecase $updateMenuItemUsecase
    ) {
    }

    public function __invoke(string $menuItemId, Request $request)
    {
        $data = $request->validate([
            'name'        => 'sometimes|array',
            'description' => 'nullable|array',
            'price'      => 'sometimes|numeric',
            'allergens'  => 'nullable|array',
            'images'     => 'nullable|array',
            'sort_order' => 'nullable|integer',
            'is_active'  => 'nullable|boolean'
        ]);

        $menuItem = $this->updateMenuItemUsecase->execute($menuItemId, $data);

        return response()->json([
            'message' => 'Menu item updated successfully',
            'data'    => [
                'id'          => $menuItem->getId(),
                'category_id' => $menuItem->getCategoryId(),
                'name'        => $menuItem->getName(),
                'description' => $menuItem->getDescription(),
                'price'       => $menuItem->getPrice(),
                'allergens'   => $menuItem->getAllergens(),
                'images'      => $menuItem->getImages(),
                'sort_order'  => $menuItem->getSortOrder(),
                'is_active'   => $menuItem->isActive()
            ]
        ]);
    }
} 