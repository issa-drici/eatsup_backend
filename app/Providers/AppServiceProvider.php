<?php

namespace App\Providers;

use App\Domain\Repositories\FileRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Domain\Repositories\MenuCategoryRepositoryInterface;
use App\Domain\Repositories\MenuItemRepositoryInterface;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Infrastructure\Repositories\EloquentMenuCategoryRepository;
use App\Infrastructure\Repositories\EloquentMenuItemRepository;
use App\Infrastructure\Repositories\EloquentMenuRepository;
use App\Infrastructure\Repositories\EloquentRestaurantRepository;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Domain\Repositories\QrCodeRepositoryInterface;
use App\Infrastructure\Repositories\EloquentQrCodeRepository;
use App\Domain\Repositories\QrCodeSessionRepositoryInterface;
use App\Infrastructure\Repositories\EloquentFileRepository;
use App\Infrastructure\Repositories\EloquentQrCodeSessionRepository;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Infrastructure\Repositories\EloquentUserRepository;
use App\Domain\Repositories\WebsiteRepositoryInterface;
use App\Infrastructure\Repositories\EloquentWebsiteRepository;

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
        $this->app->bind(QrCodeSessionRepositoryInterface::class, EloquentQrCodeSessionRepository::class);
        $this->app->bind(FileRepositoryInterface::class, EloquentFileRepository::class);
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(WebsiteRepositoryInterface::class, EloquentWebsiteRepository::class);
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
