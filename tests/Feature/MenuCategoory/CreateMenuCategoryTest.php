<?php

use App\Infrastructure\Models\MenuModel;
use App\Infrastructure\Models\RestaurantModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('creates a menu category successfully', function () {
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

    // 4. Simuler une requête d'authentification
    Auth::login($user);

    // 5. Envoyer une requête POST pour créer une catégorie de menu
    $response = $this->postJson('/api/menu-categories', [
        'menu_id' => $menu->id,
        'name' => ['en' => 'Starters', 'fr' => 'Entrées'],
        'description' => ['en' => 'Appetizers', 'fr' => 'Hors-d\'œuvre'],
        'sort_order' => 1,
    ]);

    // 6. Vérifier la réponse
    $response->assertStatus(201);
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

it('fails to create a menu category if the menu does not belong to the user', function () {
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

    // 4. Simuler une requête d'authentification pour l'utilisateur A
    Auth::login($userA);

    // 5. Essayer de créer une catégorie pour un menu qui ne lui appartient pas
    $response = $this->postJson('/api/menu-categories', [
        'menu_id' => $menu->id,
        'name' => ['en' => 'Starters', 'fr' => 'Entrées'],
        'description' => ['en' => 'Appetizers', 'fr' => 'Hors-d\'œuvre'],
        'sort_order' => 1,
    ]);

    // 6. Vérifier que l'accès est refusé
    $response->assertStatus(403);
});
