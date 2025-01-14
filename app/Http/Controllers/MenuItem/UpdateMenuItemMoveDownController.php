<?php

namespace App\Http\Controllers\MenuItem;

use App\Application\Usecases\MenuItem\UpdateMenuItemMoveDownUsecase;
use App\Http\Controllers\Controller;

class UpdateMenuItemMoveDownController extends Controller
{
    public function __construct(
        private UpdateMenuItemMoveDownUsecase $updateMenuItemMoveDownUsecase
    ) {
    }

    public function __invoke(string $menuItemId)
    {
        $menuItem = $this->updateMenuItemMoveDownUsecase->execute($menuItemId);

        return response()->json([
            'message' => 'Menu item moved down successfully',
            'data' => [
                'id' => $menuItem->getId(),
                'category_id' => $menuItem->getCategoryId(),
                'name' => $menuItem->getName(),
                'sort_order' => $menuItem->getSortOrder(),
            ]
        ]);
    }
}