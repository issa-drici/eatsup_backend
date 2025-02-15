<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import de tes controllers
use App\Http\Controllers\Menu\FindAllMenusByRestaurantIdController;
use App\Http\Controllers\Menu\FindFirstMenuByRestaurantIdController;
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
use App\Http\Controllers\MenuCategory\DeleteMenuCategoryByIdController;
use App\Http\Controllers\QrCodeSession\CountQrCodeSessionsByRestaurantIdController;
use App\Http\Controllers\QrCodeSession\CreateQrCodeSessionController;
use App\Http\Controllers\MenuItem\UpdateMenuItemController;
use App\Http\Controllers\MenuItem\FindMenuItemByIdController;
use App\Http\Controllers\MenuItem\UpdateMenuItemMoveUpController;
use App\Http\Controllers\MenuItem\UpdateMenuItemMoveDownController;
use App\Http\Controllers\MenuCategory\UpdateMenuCategoryMoveUpController;
use App\Http\Controllers\MenuCategory\UpdateMenuCategoryMoveDownController;
use App\Http\Controllers\Restaurant\UpdateRestaurantController;
use App\Http\Controllers\Menu\FindMenuByIdController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Website\FindWebsiteByRestaurantIdController;
use App\Http\Controllers\Website\UpdateWebsiteController;
use App\Http\Controllers\Website\FindWebsiteBySlugPublicController;
use App\Http\Controllers\Menu\UpdateMenuController;
use App\Http\Controllers\WebsiteSession\CountWebsiteSessionsByRestaurantIdController;
use App\Http\Controllers\WebsiteSession\CreateWebsiteSessionController;
use App\Http\Controllers\Restaurant\FindMenuInfosHomeByRestaurantIdController;

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

        // Mise à jour d'une catégorie
        Route::put('/{menuCategoryId}/update', UpdateMenuCategoryController::class)
            ->name('menu-categories.update');
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

    Route::delete('/menuCategory/{menuCategoryId}/delete', DeleteMenuCategoryByIdController::class)
        ->name('menu-categories.delete');

    Route::get('/restaurant/{restaurantId}/qrCodeSessions/count', CountQrCodeSessionsByRestaurantIdController::class)
        ->name('qr-code-sessions.count-by-restaurant-id');

    Route::post('/menuItem/{menuItemId}/update', UpdateMenuItemController::class)
        ->name('menu-items.update');

    Route::get('/menuItem/{menuItemId}', FindMenuItemByIdController::class)
        ->name('menu-items.find-by-id');

    Route::put('/menuItem/{menuItemId}/moveUp', UpdateMenuItemMoveUpController::class)
        ->name('menu-items.move-up');

    Route::put('/menuItem/{menuItemId}/moveDown', UpdateMenuItemMoveDownController::class)
        ->name('menu-items.move-down');

    Route::put('/menuCategory/{menuCategoryId}/moveUp', UpdateMenuCategoryMoveUpController::class)
        ->name('menu-categories.move-up');

    Route::put('/menuCategory/{menuCategoryId}/moveDown', UpdateMenuCategoryMoveDownController::class)
        ->name('menu-categories.move-down');

    Route::post('/restaurant/{restaurantId}/update', UpdateRestaurantController::class)
        ->name('restaurants.update');

    Route::get('/menu/{menuId}', FindMenuByIdController::class)
        ->name('menu.find-by-id');

    Route::get('/restaurant/{restaurantId}/website', FindWebsiteByRestaurantIdController::class)
        ->name('website.find-by-restaurant-id');

    Route::post('/restaurant/{restaurantId}/website/update', UpdateWebsiteController::class)
        ->name('website.update');

    Route::get('/restaurant/{restaurantId}/websiteSessions/count', CountWebsiteSessionsByRestaurantIdController::class)
        ->name('website-sessions.count');
    // Routes d'abonnement
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::post('/cancel-subscription', [SubscriptionController::class, 'cancel']);
    Route::get('/subscription/plans', [SubscriptionController::class, 'getPlans']);

    Route::post('/restaurant/{restaurantId}/menu/{menuId}/update', UpdateMenuController::class);

    Route::get('/restaurant/{restaurantId}/menu-infos-home', FindMenuInfosHomeByRestaurantIdController::class)
        ->name('restaurant.menu-infos-home');
});

// Route publique pour les QR codes
Route::get('/qr-code/{qrCodeId}', FindQrCodeByIdController::class)
    ->name('qr-codes.find-by-id');

// Route publique pour créer une session QR code
Route::post('/qr-code/{qrCodeId}/qrCodeSession/create', CreateQrCodeSessionController::class)
    ->name('qr-code-sessions.create');

// Dans le groupe des routes publiques (en dehors du middleware auth:sanctum)
Route::get('/menu/{menuId}/menuItems/groupedByCategoryName', FindAllMenuItemsByMenuIdGroupedByCategoryNameController::class)
    ->name('menu-items.grouped-by-category');

// Dans le groupe des routes publiques (en dehors du middleware auth:sanctum)
Route::get('/public/restaurant/{restaurantId}/menu/first', FindFirstMenuByRestaurantIdController::class)
    ->name('menu.find-first-by-restaurant-id');

// Route publique pour accéder au site web d'un restaurant
Route::get('/public/type/{typeSlug}/ville/{citySlug}/name/{nameSlug}/website', FindWebsiteBySlugPublicController::class)
    ->name('website.public.find-by-slug');

// Route publique pour créer une session website
Route::post('/website/{websiteId}/websiteSession/create', CreateWebsiteSessionController::class)
    ->name('website-sessions.create');
