<?php

namespace App\Application\Usecases\MenuItem;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Domain\Entities\MenuItem;
use App\Domain\Repositories\MenuItemRepositoryInterface;
use App\Domain\Repositories\MenuCategoryRepositoryInterface;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Application\Usecases\File\CreateFileUsecase;
use App\Application\Usecases\Translation\TranslateTextToMultipleLanguagesUsecase;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\PlanLimitException;

class CreateMenuItemUsecase
{
    public function __construct(
        private MenuItemRepositoryInterface $menuItemRepository,
        private MenuCategoryRepositoryInterface $menuCategoryRepository,
        private MenuRepositoryInterface $menuRepository,
        private RestaurantRepositoryInterface $restaurantRepository,
        private CreateFileUsecase $createFileUsecase,
        private TranslateTextToMultipleLanguagesUsecase $translateUsecase
    ) {
    }

    public function execute(array $data): MenuItem
    {
        return DB::transaction(function () use ($data) {
            // 1. Auth
            $user = Auth::user();
            if (!$user) {
                throw new UnauthorizedException("User not authenticated.");
            }

            // 2. Rôle
            if (!in_array($user->role, ['admin', 'restaurant_owner', 'franchise_manager'])) {
                throw new UnauthorizedException("User not allowed to create menu items.");
            }

            // 3. Vérifier la catégorie
            $category = $this->menuCategoryRepository->findById($data['category_id']);
            if (!$category) {
                throw new \Exception("Menu category not found.");
            }

            // 4. Vérifier le menu
            $menu = $this->menuRepository->findById($category->getMenuId());
            if (!$menu) {
                throw new \Exception("Menu not found.");
            }

            // 5. Si l'utilisateur n'est pas admin ou franchise_manager, vérifier qu'il est propriétaire
            if (!in_array($user->role, ['admin', 'franchise_manager'])) {
                $restaurant = $this->restaurantRepository->findByOwnerId($user->id);
                if (!$restaurant || $restaurant->getId() !== $menu->getRestaurantId()) {
                    throw new UnauthorizedException("You do not have permission to create menu items in this menu.");
                }
            }

            // 6. Limitation par plan
            $countExisting = $this->menuItemRepository->findByMenuIdAndCount($menu->getId());
            $maxAllowed = $this->getMaxItemsByPlan($user->user_plan);
            if ($countExisting >= $maxAllowed) {
                throw new PlanLimitException(trans('exceptions.plan_limit.menu_items', ['plan' => $user->user_plan]));
            }

            // 7. Gérer les images
            $imageFiles = [];
            if (isset($data['images']) && is_array($data['images'])) {
                $folderUuid = Str::uuid()->toString();
                $securePath = "menu-items/images/{$folderUuid}";
                
                foreach ($data['images'] as $image) {
                    $file = $this->createFileUsecase->execute($image, $securePath);
                    $imageFiles[] = [
                        'id' => $file->getId(),
                        'url' => $file->getUrl()
                    ];
                }
            }

            // 8. Récupérer le plus grand sort_order
            $maxSortOrder = $this->menuItemRepository->getMaxSortOrderByCategoryId($data['category_id']);
            $nextSortOrder = $maxSortOrder + 1;

            // Traduire les champs multilingues
            $translatedName = $this->translateUsecase->execute($data['name']);
            $translatedDescription = null;
            $translatedAllergens = null;

            if (isset($data['description'])) {
                $translatedDescription = $this->translateUsecase->execute($data['description']);
            }

            if (isset($data['allergens'])) {
                $translatedAllergens = $this->translateUsecase->execute($data['allergens']);
            }

            // 9. Créer l'entité
            $menuItem = new MenuItem(
                id: Str::uuid()->toString(),
                categoryId: $data['category_id'],
                name: $translatedName,
                description: $translatedDescription,
                price: $data['price'],
                allergens: $translatedAllergens,
                images: $imageFiles,
                isActive: $data['is_active'] ?? true,
                sortOrder: $nextSortOrder
            );

            // 10. Persister
            return $this->menuItemRepository->create($menuItem);
        });
    }

    private function getMaxItemsByPlan(string $plan): int
    {
        return match ($plan) {
            'basic' => 15,
            'premium' => 50,
            'enterprise' => PHP_INT_MAX,
            default => 0,
        };
    }
}
