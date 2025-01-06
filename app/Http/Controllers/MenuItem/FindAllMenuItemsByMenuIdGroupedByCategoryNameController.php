<?php

namespace App\Http\Controllers\MenuItem;

use App\Application\Usecases\MenuItem\FindAllMenuItemsByMenuIdGroupedByCategoryNameUsecase;
use App\Http\Controllers\Controller;

class FindAllMenuItemsByMenuIdGroupedByCategoryNameController extends Controller
{
    public function __construct(
        private FindAllMenuItemsByMenuIdGroupedByCategoryNameUsecase $findAllMenuItemsByMenuIdGroupedByCategoryNameUsecase
    ) {
    }

    public function __invoke(string $menuId)
    {
        $groupedItems = $this->findAllMenuItemsByMenuIdGroupedByCategoryNameUsecase->execute($menuId);

        return response()->json([
            'message' => 'Menu items grouped by category retrieved successfully',
            'data'    => $groupedItems
        ]);
    }
} 