<?php

namespace App\Http\Controllers\Auth;

use App\Application\Usecases\Menu\CreateMenuUsecase;
use App\Application\Usecases\Restaurant\CreateRestaurantUsecase;
use App\Application\Usecases\Website\CreateWebsiteUsecase;
use App\Domain\Entities\Restaurant;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): Response
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'user_plan' => ['required', 'string', 'in:basic,premium,elite'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
            'user_plan' => $request->user_plan,
            'email_verified_at' => '2000-01-01 00:00:00',
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Création du restaurant
        $createRestaurantUsecase = app(CreateRestaurantUsecase::class);
        $restaurant = $createRestaurantUsecase->execute([
            'name' => $user->name,
            'owner_id' => $user->id
        ]);

        // Création du menu
        $createMenuUsecase = app(CreateMenuUsecase::class);
        $menu = $createMenuUsecase->execute([
            'name' => 'Menu 1',
            'restaurant_id' => $restaurant->getId()
        ]);

        // Création du site web
        $createWebsiteUsecase = app(CreateWebsiteUsecase::class);
        $website = $createWebsiteUsecase->execute([
            'restaurant_id' => $restaurant->getId(),
            'title' => ['fr' => $user->name, 'en' => $user->name],
            'description' => ['fr' => 'Bienvenue sur notre site', 'en' => 'Welcome to our website'],
            'theme_config' => [
                'colors' => [
                    'primary' => '#4F46E5',
                    'secondary' => '#10B981'
                ]
            ]
        ]);

        return response()->noContent();
    }
}
