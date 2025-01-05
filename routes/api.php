<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import de tes controllers
use App\Http\Controllers\MenuCategory\CreateMenuCategoryController;
use App\Http\Controllers\MenuCategory\UpdateMenuCategoryController;
use App\Http\Controllers\Menu\FindAllMenusByRestaurantIdController;
use App\Http\Controllers\MenuCategories\CountMenuCategoriesByMenuIdController;
use App\Http\Controllers\MenuItems\CountMenuItemsByMenuIdController;

// Routes nécessitant l'authentification via Sanctum
Route::middleware(['auth:sanctum'])->group(function () {

    // Route déjà existante : /api/user
    Route::get('/user', function (Request $request) {
        return $request->user()->load('restaurant');
    });

    // Groupons ici toutes les routes liées aux "menu-categories"
    Route::prefix('menu-categories')->group(function () {

        // Création d'une catégorie
        // POST /api/menu-categories
        Route::post('/', CreateMenuCategoryController::class)->name('menu-categories.create');

        // Mise à jour d'une catégorie (avec l'ID en paramètre)
        // PUT /api/menu-categories/{id}
        Route::put('/{id}', UpdateMenuCategoryController::class)->name('menu-categories.update');
    });

    Route::get('/restaurant/{restaurantId}/menus', FindAllMenusByRestaurantIdController::class)
        ->name('menus.find-all.by-restaurant-id');


    Route::prefix('menu')->group(function () {

        // Compte le nombre de catégories dans un menu
        Route::get('/{menuId}/menuCategories/count', CountMenuCategoriesByMenuIdController::class)->name('menu-categories.count.by-menu-id');

        // Compte le nombre d'items dans un menu
        Route::get('/{menuId}/menuItems/count', CountMenuItemsByMenuIdController::class)->name('menu-items.count.by-menu-id');
    });
});
