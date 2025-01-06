<?php

use App\Infrastructure\Models\MenuCategoriesModel;
use App\Infrastructure\Models\MenuModel;
use App\Infrastructure\Models\RestaurantModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

it('updates a menu category successfully', function () {
    // 1. Créer un utilisateur propriétaire d'un restaurant
    $user = User::factory()->create([
        'role' => 'restaurant_owner',
        'user_plan' => 'basic',
    ]);

    // 2. Créer un restaurant lié à cet utilisateur
    $restaurant = RestaurantModel::factory()->create([
        'owner_id' => $user->id,
    ]);

    // 3. Créer un menu lié à ce restaurant
    $menu = MenuModel::factory()->create([
        'restaurant_id' => $restaurant->id,
    ]);

    // 4. Créer une catégorie liée à ce menu
    $menuCategories = MenuCategoriesModel::factory()->create([
        'menu_id' => $menu->id,
    ]);

    // 5. Simuler une requête d'authentification
    Auth::login($user);

    // 6. Envoyer une requête PUT pour mettre à jour la catégorie
    $response = $this->putJson("/api/menu-categories/{$menuCategories->id}", [
        'name' => ['en' => 'Main Courses', 'fr' => 'Plats Principaux'],
        'description' => ['en' => 'Main dishes', 'fr' => 'Plats principaux'],
        'sort_order' => 2,
    ]);

    // 7. Vérifier la réponse
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'message',
        'data' => [
            'id',
            'menu_id',
            'name',
            'description',
            'sort_order',
        ],
    ]);
});

it('fails to update a menu category if the menu does not belong to the user', function () {
    // 1. Créer deux utilisateurs (A et B)
    $userA = User::factory()->create(['role' => 'restaurant_owner']);
    $userB = User::factory()->create(['role' => 'restaurant_owner']);

    // 2. Créer un restaurant pour l'utilisateur B
    $restaurant = RestaurantModel::factory()->create([
        'owner_id' => $userB->id,
    ]);

    // 3. Créer un menu pour le restaurant de B
    $menu = MenuModel::factory()->create([
        'restaurant_id' => $restaurant->id,
    ]);

    // 4. Créer une catégorie pour le menu de B
    $menuCategories = MenuCategoriesModel::factory()->create([
        'menu_id' => $menu->id,
    ]);

    // 5. Simuler une requête d'authentification pour l'utilisateur A
    Auth::login($userA);

    // 6. Essayer de mettre à jour la catégorie
    $response = $this->putJson("/api/menu-categories/{$menuCategories->id}", [
        'name' => ['en' => 'Main Courses', 'fr' => 'Plats Principaux'],
        'description' => ['en' => 'Main dishes', 'fr' => 'Plats principaux'],
        'sort_order' => 2,
    ]);

    // 7. Vérifier que l'accès est refusé
    $response->assertStatus(403);
});
