<?php

namespace App\Http\Controllers\MenuItem;

use App\Application\Usecases\MenuItem\DeleteMenuItemByIdUsecase;
use App\Http\Controllers\Controller;

class DeleteMenuItemByIdController extends Controller
{
    public function __construct(
        private DeleteMenuItemByIdUsecase $deleteMenuItemByIdUsecase
    ) {
    }

    public function __invoke(string $menuItemId)
    {
        $this->deleteMenuItemByIdUsecase->execute($menuItemId);

        return response()->json([
            'message' => 'Menu item deleted successfully'
        ]);
    }
} 