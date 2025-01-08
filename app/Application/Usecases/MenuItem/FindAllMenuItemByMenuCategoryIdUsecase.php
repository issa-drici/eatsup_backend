<?php

namespace App\Application\Usecases\MenuItem;

use Illuminate\Support\Facades\Auth;
use App\Domain\Repositories\MenuItemRepositoryInterface;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Domain\Repositories\MenuCategoryRepositoryInterface;
use App\Exceptions\UnauthorizedException;

class FindAllMenuItemsByMenuCategoryIdUsecase
{
    public function __construct(
        private MenuItemRepositoryInterface $menuItemRepository,
        private MenuRepositoryInterface $menuRepository,
        private MenuCategoryRepositoryInterface $menuCategoryRepository
    ) {
    }

    public function execute(string $menuCategoryId): array
    {
        // 1. Vérifier l'authentification
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }


        // 3. Vérifier si la catégorie existe et appartient au menu
        $category = $this->menuCategoryRepository->findById($menuCategoryId);
        if (!$category) {
            throw new \Exception("Menu category not found.");
        }

        // 4. Vérifier les permissions selon le rôle
        if (!in_array($user->role, ['admin', 'restaurant_owner', 'franchise_manager'])) {
            throw new UnauthorizedException("You do not have access to this menu.");
        }

        // 5. Récupérer et retourner les items
        $items = $this->menuItemRepository->findAllByMenuCategoryId($menuCategoryId);

        // 6. Formater les données pour la réponse
        return array_map(function ($item) {
            return [
                'id' => $item->getId(),
                'menu_category_id' => $item->getMenuCategoriesId(),
                'name' => $item->getName(),
                'description' => $item->getDescription(),
                'price' => $item->getPrice(),
                'image' => $item->getImage(),
                'sort_order' => $item->getSortOrder(),
                'status' => $item->getStatus(),
            ];
        }, $items);
    }
} 