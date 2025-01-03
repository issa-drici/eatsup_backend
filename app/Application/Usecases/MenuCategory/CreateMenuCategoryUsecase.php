<?php

namespace App\Application\Usecases\MenuCategory;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Domain\Entities\MenuCategory;
use App\Domain\Repositories\MenuCategoryRepositoryInterface;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\PlanLimitException;

class CreateMenuCategoryUsecase
{
    public function __construct(
        private MenuCategoryRepositoryInterface $menuCategoryRepository,
        private MenuRepositoryInterface $menuRepository,
        private RestaurantRepositoryInterface $restaurantRepository
    ) {
        //
    }

    public function execute(array $data): MenuCategory
    {
        // 1. Vérifier l’auth
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }

        // 2. Vérifier le rôle
        if (!in_array($user->role, ['admin', 'restaurant_owner', 'franchise_manager'])) {
            throw new UnauthorizedException("User not allowed to create categories.");
        }

        // 3. Récupérer le restaurant lié à cet utilisateur
        $restaurant = $this->restaurantRepository->findByOwnerId($user->id);
        if (!$restaurant) {
            throw new UnauthorizedException("No restaurant found for this user.");
        }

        // 4. Vérifier si le menu existe et appartient au restaurant
        $menuId = $data['menu_id'];
        $menu = $this->menuRepository->findById($menuId);
        if (!$menu || $menu->getRestaurantId() !== $restaurant->getId()) {
            throw new UnauthorizedException("This menu does not belong to your restaurant.");
        }

        // 5. Limitation par plan
        $countExisting = $this->menuCategoryRepository->findByMenuIdAndCount($menuId);
        $maxAllowed = $this->getMaxCategoriesByPlan($user->user_plan);
        if ($countExisting >= $maxAllowed) {
            throw new PlanLimitException("Limit of categories reached for plan: {$user->user_plan}");
        }

        // 6. Créer l’entité
        $menuCategory = new MenuCategory(
            id: Str::uuid()->toString(),
            menuId: $menuId,
            name: $data['name'],
            description: $data['description'] ?? null,
            sortOrder: $data['sort_order'] ?? 0
        );

        // 7. Persister
        return $this->menuCategoryRepository->create($menuCategory);
    }

    private function getMaxCategoriesByPlan(?string $plan): int
    {
        return match ($plan) {
            'basic' => 5,
            'premium' => 10,
            'enterprise' => 9999,
            default => 5,
        };
    }
}
