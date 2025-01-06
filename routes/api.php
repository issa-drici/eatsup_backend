<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import de tes controllers
use App\Http\Controllers\Menu\FindAllMenusByRestaurantIdController;


use App\Http\Controllers\MenuCategory\CountMenuCategoriesByMenuIdController;
use App\Http\Controllers\MenuCategory\CreateMenuCategoryController;
use App\Http\Controllers\MenuCategory\FindAllMenuCategoriesByMenuIdController;
use App\Http\Controllers\MenuCategory\FindMenuCategoryByIdController;
use App\Http\Controllers\MenuCategory\UpdateMenuCategoryController;
use App\Http\Controllers\MenuItem\CountMenuItemsByMenuIdController;
use App\Http\Controllers\MenuItem\FindAllMenuItemsByMenuCategoryIdController;

// Routes nécessitant l'authentification via Sanctum
Route::middleware(['auth:sanctum'])->group(function () {

    // Route déjà existante : /api/user
    Route::get('/user', function (Request $request) {
        return $request->user()->load('restaurant');
    });

    // Groupons ici toutes les routes liées aux "menu-category"
    Route::prefix('menu-category')->group(function () {

        // Création d'une catégorie
        // POST /api/menu-category
        Route::post('/', CreateMenuCategoryController::class)->name('menu-category.create');

        // Mise à jour d'une catégorie (avec l'ID en paramètre)
        // PUT /api/menu-category/{id}
        Route::put('/{id}', UpdateMenuCategoryController::class)->name('menu-category.update');
    });

    Route::get('/restaurant/{restaurantId}/menus', FindAllMenusByRestaurantIdController::class)
        ->name('menus.find-all.by-restaurant-id');


    Route::prefix('menu')->group(function () {

        // Compte le nombre de catégories dans un menu
        Route::get('/{menuId}/menuCategories/count', CountMenuCategoriesByMenuIdController::class)->name('menu-categories.count.by-menu-id');

        // Liste toutes les catégories d'un menu
        Route::get('/{menuId}/menuCategories', FindAllMenuCategoriesByMenuIdController::class)
            ->name('menu-categories.find-by-menu-id');

        

        // Compte le nombre d'items dans un menu
        Route::get('/{menuId}/menuItems/count', CountMenuItemsByMenuIdController::class)->name('menu-items.count.by-menu-id');
    });


    Route::prefix('menuCategory')->group(function () {

        // Récupère une catégorie spécifique d'un menu
        Route::get('/{menuCategoryId}', FindMenuCategoryByIdController::class)
            ->name('menu-category.find-by-id');

        // Liste tous les items d'une catégorie de menu
        Route::get('/{menuCategoryId}/items', FindAllMenuItemsByMenuCategoryIdController::class)
            ->name('menu-items.find-by-menu-category-id');
    });
});
