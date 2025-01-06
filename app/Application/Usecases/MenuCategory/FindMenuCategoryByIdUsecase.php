<?php

namespace App\Application\Usecases\MenuCategory;

use App\Domain\Entities\MenuCategory;
use Illuminate\Support\Facades\Auth;
use App\Domain\Repositories\MenuCategoryRepositoryInterface;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Exceptions\UnauthorizedException;


class FindMenuCategoryByIdUsecase
{
    public function __construct(
        private MenuCategoryRepositoryInterface $menuCategoryRepository,
        private MenuRepositoryInterface $menuRepository
    ) {}

    public function execute(string $categoryId): MenuCategory
    {
        // 1. Vérifier l'authentification
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }


        // 3. Récupérer la catégorie
        $category = $this->menuCategoryRepository->findById($categoryId);
        if (!$category) {
            throw new \Exception("Menu category not found.");
        }



        // 5. Vérifier les permissions selon le rôle
        if (!in_array($user->role, ['admin', 'restaurant_owner', 'franchise_manager'])) {
            throw new UnauthorizedException("You do not have access to this menu category.");
        }

        return $category;
    }
}
