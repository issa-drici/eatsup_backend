<?php

namespace App\Http\Controllers\MenuCategory;

use App\Application\Usecases\MenuCategory\DeleteMenuCategoryByIdUsecase;
use App\Http\Controllers\Controller;

class DeleteMenuCategoryByIdController extends Controller
{
    public function __construct(
        private DeleteMenuCategoryByIdUsecase $deleteMenuCategoryByIdUsecase
    ) {
    }

    public function __invoke(string $menuCategoryId)
    {
        $this->deleteMenuCategoryByIdUsecase->execute($menuCategoryId);

        return response()->json([
            'message' => 'Menu category deleted successfully'
        ]);
    }
} 