<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Repositories\MenuCategoryRepositoryInterface;
use App\Domain\Repositories\MenuItemRepositoryInterface;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Infrastructure\Repositories\EloquentMenuCategoryRepository;
use App\Infrastructure\Repositories\EloquentMenuItemRepository;
use App\Infrastructure\Repositories\EloquentMenuItemsRepository;
use App\Infrastructure\Repositories\EloquentMenuRepository;
use App\Infrastructure\Repositories\EloquentRestaurantRepository;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\URL;
use App\Domain\Repositories\QrCodeRepositoryInterface;
use App\Infrastructure\Repositories\EloquentQrCodeRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MenuCategoryRepositoryInterface::class, EloquentMenuCategoryRepository::class);
        $this->app->bind(MenuRepositoryInterface::class, EloquentMenuRepository::class);
        $this->app->bind(MenuItemRepositoryInterface::class, EloquentMenuItemRepository::class);
        $this->app->bind(RestaurantRepositoryInterface::class, EloquentRestaurantRepository::class);
        $this->app->bind(QrCodeRepositoryInterface::class, EloquentQrCodeRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url') . "/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}
