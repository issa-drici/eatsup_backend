<?php

namespace App\Http\Controllers\MenuItem;

use App\Application\Usecases\MenuItem\UpdateMenuItemMoveUpUsecase;
use App\Http\Controllers\Controller;

class UpdateMenuItemMoveUpController extends Controller
{
    public function __construct(
        private UpdateMenuItemMoveUpUsecase $updateMenuItemMoveUpUsecase
    ) {
    }

    public function __invoke(string $menuItemId)
    {
        $menuItem = $this->updateMenuItemMoveUpUsecase->execute($menuItemId);

        return response()->json([
            'message' => 'Menu item moved up successfully',
            'data' => [
                'id' => $menuItem->getId(),
                'category_id' => $menuItem->getCategoryId(),
                'name' => $menuItem->getName(),
                'sort_order' => $menuItem->getSortOrder(),
            ]
        ]);
    }
} 