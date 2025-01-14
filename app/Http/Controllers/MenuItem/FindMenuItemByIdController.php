<?php

namespace App\Http\Controllers\MenuItem;

use App\Application\Usecases\MenuItem\FindMenuItemByIdUsecase;
use App\Http\Controllers\Controller;

class FindMenuItemByIdController extends Controller
{
    public function __construct(
        private FindMenuItemByIdUsecase $findMenuItemByIdUsecase
    ) {
    }

    public function __invoke(string $menuItemId)
    {
        $menuItem = $this->findMenuItemByIdUsecase->execute($menuItemId);

        return response()->json([
            'message' => 'Menu item found successfully',
            'data'    => [
                'id'          => $menuItem->getId(),
                'category_id' => $menuItem->getCategoryId(),
                'name'        => $menuItem->getName(),
                'description' => $menuItem->getDescription(),
                'price'       => $menuItem->getPrice(),
                'allergens'   => $menuItem->getAllergens(),
                'images'      => $menuItem->getImages(),
                'is_active'   => $menuItem->isActive(),
                'sort_order'  => $menuItem->getSortOrder(),
            ]
        ]);
    }
} 