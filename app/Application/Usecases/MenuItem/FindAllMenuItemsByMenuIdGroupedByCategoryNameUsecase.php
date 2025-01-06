<?php

namespace App\Application\Usecases\MenuItem;

use Illuminate\Support\Facades\Auth;
use App\Domain\Repositories\MenuItemRepositoryInterface;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Domain\Repositories\MenuCategoryRepositoryInterface;
use App\Exceptions\UnauthorizedException;

class FindAllMenuItemsByMenuIdGroupedByCategoryNameUsecase
{
    public function __construct(
        private MenuItemRepositoryInterface $menuItemRepository,
        private MenuRepositoryInterface $menuRepository,
        private MenuCategoryRepositoryInterface $menuCategoryRepository
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

        // 4. Récupérer toutes les catégories du menu
        $categories = $this->menuCategoryRepository->findAllByMenuId($menuId);
        
        // 5. Préparer le résultat groupé
        $result = [];
        
        foreach ($categories as $category) {
            $items = $this->menuItemRepository->findAllByMenuCategoryId($category->getId());
            
            $result[] = [
                'category' => [
                    'id' => $category->getId(),
                    'name' => $category->getName(),
                    'description' => $category->getDescription(),
                    'sort_order' => $category->getSortOrder()
                ],
                'items' => array_map(function ($item) {
                    return [
                        'id' => $item->getId(),
                        'category_id' => $item->getCategoryId(),
                        'name' => $item->getName(),
                        'description' => $item->getDescription(),
                        'price' => $item->getPrice(),
                        'allergens' => $item->getAllergens(),
                        'images' => $item->getImages(),
                        'is_active' => $item->isActive(),
                        'sort_order' => $item->getSortOrder(),
                    ];
                }, $items)
            ];
        }

        // 6. Trier les catégories par sort_order
        usort($result, function ($a, $b) {
            return $a['category']['sort_order'] <=> $b['category']['sort_order'];
        });

        return $result;
    }
} 