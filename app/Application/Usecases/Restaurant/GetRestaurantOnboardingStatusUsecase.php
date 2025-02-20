<?php

namespace App\Application\Usecases\Restaurant;

use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Domain\Repositories\MenuCategoryRepositoryInterface;
use App\Domain\Repositories\MenuItemRepositoryInterface;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Domain\Repositories\WebsiteRepositoryInterface;
use App\Exceptions\UnauthorizedException;
use Illuminate\Support\Facades\Auth;

class GetRestaurantOnboardingStatusUsecase
{
    public function __construct(
        private RestaurantRepositoryInterface $restaurantRepository,
        private MenuCategoryRepositoryInterface $menuCategoryRepository,
        private MenuItemRepositoryInterface $menuItemRepository,
        private MenuRepositoryInterface $menuRepository,
        private WebsiteRepositoryInterface $websiteRepository
    ) {}

    public function execute(string $restaurantId): array
    {
        // Vérification de l'authentification
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }

        // Vérification des permissions
        if ($user->role === 'restaurant_owner') {
            $restaurant = $this->restaurantRepository->findByOwnerId($user->id);
            if (!$restaurant || $restaurant->getId() !== $restaurantId) {
                throw new UnauthorizedException("You do not have access to this restaurant.");
            }
        }

        // Récupérer les données du restaurant et du menu
        $restaurant = $this->restaurantRepository->findById($restaurantId);
        $website = $this->websiteRepository->findByRestaurantId($restaurantId);
        $menus = $this->menuRepository->findByRestaurantId($restaurantId);
        $menu = !empty($menus) ? $menus[0] : null;
        $menuId = $menu ? $menu->getId() : null;
        $websiteId = $website ? $website->getId() : null;

        // Vérifier les différentes étapes
        $hasCategories = $this->menuCategoryRepository->countByRestaurantId($restaurantId) > 0;
        $hasMenuItems = $this->menuItemRepository->countByRestaurantId($restaurantId) > 0;
        $hasLogo = $restaurant->getLogoId() !== null;
        $hasOpeningHours = !empty($website->getOpeningHours());
        $hasAds = $menu && !empty($menu->getBanners());
        $hasSocialLinks = !empty($restaurant->getSocialLinks());

        return [
            [
                'id' => 1,
                'title' => 'Créez votre première catégorie de plats',
                'completed' => $hasCategories,
                'url' => "/admin/restaurant/{$restaurantId}/menu/{$menuId}/category/create",
                'icon' => '📑'
            ],
            [
                'id' => 2,
                'title' => 'Ajoutez votre premier plat au menu',
                'completed' => $hasMenuItems,
                'url' => "/admin/restaurant/{$restaurantId}/menu/{$menuId}/items",
                'icon' => '🍽️'
            ],
            [
                'id' => 3,
                'title' => 'Mettez en avant une offre sur votre menu',
                'completed' => $hasAds,
                'url' => "/admin/restaurant/{$restaurantId}/menu/{$menuId}/update",
                'icon' => '🎯'
            ],
            [
                'id' => 4,
                'title' => 'Ajoutez le logo de votre restaurant',
                'completed' => $hasLogo,
                'url' => "/admin/restaurant/{$restaurantId}/update",
                'icon' => '🎨'
            ],
            [
                'id' => 5,
                'title' => 'Définissez vos horaires d\'ouverture',
                'completed' => $hasOpeningHours,
                'url' => "/admin/restaurant/{$restaurantId}/website/{$websiteId}/update",
                'icon' => '⏰'
            ],
            [
                'id' => 6,
                'title' => 'Partagez votre restaurant sur vos réseaux',
                'completed' => $hasSocialLinks,
                'url' => "/restaurant/{$restaurantId}/settings/social",
                'icon' => '🔗'
            ]
        ];
    }
}
