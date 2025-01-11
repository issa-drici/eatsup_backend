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
use App\Http\Controllers\MenuItem\FindAllMenuItemsByMenuIdGroupedByCategoryNameController;
use App\Http\Controllers\MenuItem\CreateMenuItemController;
use App\Http\Controllers\Restaurant\FindAllRestaurantsWithoutQRCodeController;
use App\Http\Controllers\Restaurant\FindRestaurantByIdController;
use App\Http\Controllers\QrCode\UpdateQrCodeController;
use App\Http\Controllers\QrCode\FindAllQrCodesByRestaurantIdController;
use App\Http\Controllers\Restaurant\FindAllRestaurantsController;
use App\Http\Controllers\Restaurant\FindAllRestaurantsWithQrCodeCountController;
use App\Http\Controllers\QrCode\FindQrCodeByIdController;
use App\Http\Controllers\MenuItem\DeleteMenuItemByIdController;

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

        // Création d'une catégorie de menu
        Route::post('/{menuId}/menuCategory/create', CreateMenuCategoryController::class)
            ->name('menu-category.create-with-menu-id');

        // Compte le nombre de catégories dans un menu
        Route::get('/{menuId}/menuCategories/count', CountMenuCategoriesByMenuIdController::class)->name('menu-categories.count.by-menu-id');

        // Liste toutes les catégories d'un menu
        Route::get('/{menuId}/menuCategories', FindAllMenuCategoriesByMenuIdController::class)
            ->name('menu-categories.find-by-menu-id');

        // Compte le nombre d'items dans un menu
        Route::get('/{menuId}/menuItems/count', CountMenuItemsByMenuIdController::class)->name('menu-items.count.by-menu-id');

        // Liste tous les items d'un menu groupés par catégorie
        Route::get('/{menuId}/menuItems/groupedByCategoryName', FindAllMenuItemsByMenuIdGroupedByCategoryNameController::class)
            ->name('menu-items.grouped-by-category');
    });


    Route::prefix('menuCategory')->group(function () {

        // Récupère une catégorie spécifique d'un menu
        Route::get('/{menuCategoryId}', FindMenuCategoryByIdController::class)
            ->name('menu-category.find-by-id');

        // Liste tous les items d'une catégorie de menu
        Route::get('/{menuCategoryId}/items', FindAllMenuItemsByMenuCategoryIdController::class)
            ->name('menu-items.find-by-menu-category-id');

        // Création d'un item dans une catégorie
        Route::post('/{menuCategoryId}/item/create', CreateMenuItemController::class)
            ->name('menu-items.create');
    });

    // Liste des restaurants sans QR code
    Route::get('/restaurants/without-qr-code', FindAllRestaurantsWithoutQRCodeController::class)
        ->name('restaurants.without-qr-code');

    Route::get('/restaurant/{restaurantId}', FindRestaurantByIdController::class)
        ->name('restaurant.find-by-id');

    Route::put('/associate-qr/qr-code/{qrCodeId}', UpdateQrCodeController::class)
        ->name('qr-codes.update');



    Route::get('/restaurants', FindAllRestaurantsController::class)
        ->name('restaurants.find-all');

    Route::get('/restaurants/with-qr-code-count', FindAllRestaurantsWithQrCodeCountController::class)
        ->name('restaurants.with-qr-code-count');


    // QR Codes d'un restaurant
    Route::get('/qr-code/restaurant/{restaurantId}', FindAllQrCodesByRestaurantIdController::class)
        ->name('qr-codes.find-all-by-restaurant-id');

    Route::delete('/menuItem/{menuItemId}/delete', DeleteMenuItemByIdController::class)
        ->name('menu-items.delete');
});

// Route publique pour les QR codes
Route::get('/qr-code/{qrCodeId}', FindQrCodeByIdController::class)
    ->name('qr-codes.find-by-id');

// Dans le groupe des routes publiques (en dehors du middleware auth:sanctum)
Route::get('/menu/{menuId}/menuItems/groupedByCategoryName', FindAllMenuItemsByMenuIdGroupedByCategoryNameController::class)
    ->name('menu-items.grouped-by-category');
