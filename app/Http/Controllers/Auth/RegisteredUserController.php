<?php

namespace App\Http\Controllers\Auth;

use App\Application\Usecases\Menu\CreateMenuUsecase;
use App\Application\Usecases\Restaurant\CreateRestaurantUsecase;
use App\Application\Usecases\Website\CreateWebsiteUsecase;
use App\Domain\Entities\Restaurant;
use App\Http\Controllers\Controller;
use App\Infrastructure\Models\EmailNotificationModel;
use App\Models\User;
use App\Models\EmailNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;
use App\Mail\WelcomeTrialStarted;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

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
            'name' => ucwords($request->name),
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
            'user_plan' => 'premium',
            'user_subscription_status' => 'trialing',
            'trial_ends_at' => Carbon::now()->addDays(30),
            'email_verified_at' => '2000-01-01 00:00:00',
        ]);
        
        event(new Registered($user));
        
        Auth::login($user);

        // Création du restaurant
        $createRestaurantUsecase = app(CreateRestaurantUsecase::class);
        $restaurant = $createRestaurantUsecase->execute([
            'name' => ucwords($user->name),
            'name_slug' => Str::slug($user->name),
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
            'title' => ['fr' => ucwords($user->name), 'en' => ucwords($user->name)],
            'description' => ['fr' => 'Bienvenue sur notre site', 'en' => 'Welcome to our website'],
            'menu_id' => $menu->getId(),
            'theme_config' => [
                'colors' => [
                    'primary' => '#4F46E5',
                    'secondary' => '#10B981'
                ]
            ]
        ]);

        // Envoyer le mail de bienvenue
        Mail::to($user->email)->send(new WelcomeTrialStarted($user));
        
        // Enregistrer l'envoi du mail
        EmailNotificationModel::create([
            'user_id' => $user->id,
            'type' => 'welcome_trial_started',
            'sent_at' => Carbon::now()
        ]);

        return response()->noContent();
    }
}
