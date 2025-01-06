<?php

namespace App\Application\Usecases\MenuCategory;

use Illuminate\Support\Facades\Auth;
use App\Domain\Repositories\MenuCategoryRepositoryInterface;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Exceptions\UnauthorizedException;

class FindAllMenuCategoriesByMenuIdUsecase
{
    public function __construct(
        private MenuCategoryRepositoryInterface $menuCategoryRepository,
        private MenuRepositoryInterface $menuRepository
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

        // 4. Récupérer et retourner les catégories
        $categories = $this->menuCategoryRepository->findAllByMenuId($menuId);

        // 5. Formater les données pour la réponse
        return array_map(function ($category) {
            return [
                'id' => $category->getId(),
                'menu_id' => $category->getMenuId(),
                'name' => $category->getName(),
                'description' => $category->getDescription(),
                'sort_order' => $category->getSortOrder(),
            ];
        }, $categories);
    }
} 