<?php

namespace App\Application\Usecases\MenuCategory;

use Illuminate\Support\Facades\Auth;
use App\Domain\Repositories\MenuCategoryRepositoryInterface;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Exceptions\UnauthorizedException;

class CountMenuCategoriesByMenuIdUsecase
{
    public function __construct(
        private MenuCategoryRepositoryInterface $menuCategoryRepository,
        private MenuRepositoryInterface $menuRepository
    ) {
        //
    }

    public function execute(string $menuId): int
    {
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }

        // Vérifier si le menu existe et appartient à un restaurant accessible
        $menu = $this->menuRepository->findById($menuId);
        if (!$menu) {
            throw new \Exception("Menu not found.");
        }

        // Si le rôle n'est pas admin ou franchise_manager, vérifier l'accès
        if (!in_array($user->role, ['admin', 'restaurant_owner', 'franchise_manager'])) {
            throw new UnauthorizedException("You do not have access to this menu.");
        }

        // Retourner le nombre de catégories
        return $this->menuCategoryRepository->findByMenuIdAndCount($menuId);
    }
}
