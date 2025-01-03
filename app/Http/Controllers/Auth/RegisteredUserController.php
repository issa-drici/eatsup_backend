<?php

namespace App\Http\Controllers\Auth;

use App\Application\Usecases\Menu\CreateMenuUsecase;
use App\Application\Usecases\Restaurant\CreateRestaurantUsecase;
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
            'user_plan' => ['required', 'string', 'in:basic,premium, elite'],
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

        // Laisser Laravel résoudre les dépendances
        $createRestaurantUsecase = app(CreateRestaurantUsecase::class);
        $restaurant = $createRestaurantUsecase->execute([
            'name' => $user->name,
            'owner_id' => $user->id
        ]);

        $createMenuUsecase = app(CreateMenuUsecase::class);
        $menu = $createMenuUsecase->execute([
            'name' => 'Menu 1',
            'restaurant_id' => $restaurant->getId()
        ]);

        return response()->noContent();
    }
}
