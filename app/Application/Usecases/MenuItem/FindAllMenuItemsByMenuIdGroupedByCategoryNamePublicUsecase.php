<?php

namespace App\Application\Usecases\MenuItem;

use App\Domain\Repositories\MenuItemRepositoryInterface;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Domain\Repositories\MenuCategoryRepositoryInterface;

class FindAllMenuItemsByMenuIdGroupedByCategoryNamePublicUsecase
{
    public function __construct(
        private MenuItemRepositoryInterface $menuItemRepository,
        private MenuRepositoryInterface $menuRepository,
        private MenuCategoryRepositoryInterface $menuCategoryRepository
    ) {
    }

    public function execute(string $menuId): array
    {
        // 1. Vérifier si le menu existe
        $menu = $this->menuRepository->findById($menuId);
        if (!$menu) {
            throw new \Exception("Menu not found.");
        }

        // 2. Récupérer toutes les catégories du menu
        $categories = $this->menuCategoryRepository->findAllByMenuId($menuId);
        
        // 3. Préparer le résultat groupé
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

        // 4. Trier les catégories par sort_order
        usort($result, function ($a, $b) {
            return $a['category']['sort_order'] <=> $b['category']['sort_order'];
        });

        return $result;
    }
}