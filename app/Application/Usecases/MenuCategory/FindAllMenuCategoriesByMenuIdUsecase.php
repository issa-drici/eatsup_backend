<?php

namespace App\Application\Usecases\MenuCategory;

use Illuminate\Support\Facades\Auth;
use App\Domain\Repositories\MenuCategoryRepositoryInterface;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Domain\Repositories\MenuItemRepositoryInterface;
use App\Exceptions\UnauthorizedException;
use App\Application\DTOs\MenuCategoryWithItemCountDTO;

class FindAllMenuCategoriesByMenuIdUsecase
{
    public function __construct(
        private MenuCategoryRepositoryInterface $menuCategoryRepository,
        private MenuRepositoryInterface $menuRepository,
        private MenuItemRepositoryInterface $menuItemRepository
    ) {
    }

    public function execute(string $menuId): array
    {
        // 1. Vérifier l'authentification
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }

        // 2. Vérifier si le menu existe
        $menu = $this->menuRepository->findById($menuId);
        if (!$menu) {
            throw new \Exception("Menu not found.");
        }

        // 3. Vérifier les permissions selon le rôle
        if (!in_array($user->role, ['admin', 'restaurant_owner', 'franchise_manager'])) {
            throw new UnauthorizedException("You do not have access to this menu.");
        }

        // 4. Récupérer les catégories
        $categories = $this->menuCategoryRepository->findAllByMenuId($menuId);

        // 5. Formater les données avec le compte d'items pour chaque catégorie
        return array_map(function ($category) {
            $itemCount = $this->menuItemRepository->countByCategoryId($category->getId());
            $dto = MenuCategoryWithItemCountDTO::fromMenuCategoryAndItemCount($category, $itemCount);
            
            return [
                'id' => $dto->id,
                'menu_id' => $dto->menu_id,
                'name' => $dto->name,
                'description' => $dto->description,
                'sort_order' => $dto->sort_order,
                'items_count' => $dto->items_count,
            ];
        }, $categories);
    }
} 