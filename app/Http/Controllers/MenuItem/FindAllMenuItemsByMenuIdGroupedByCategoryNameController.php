<?php

namespace App\Http\Controllers\MenuItem;

use App\Application\Usecases\MenuItem\FindAllMenuItemsByMenuIdGroupedByCategoryNamePublicUsecase;
use App\Http\Controllers\Controller;

class FindAllMenuItemsByMenuIdGroupedByCategoryNameController extends Controller
{
    public function __construct(
        private FindAllMenuItemsByMenuIdGroupedByCategoryNamePublicUsecase $usecase
    ) {
    }

    public function __invoke(string $menuId)
    {
        $items = $this->usecase->execute($menuId);

        return response()->json([
            'message' => 'Menu items retrieved successfully',
            'data'    => $items
        ]);
    }
} 