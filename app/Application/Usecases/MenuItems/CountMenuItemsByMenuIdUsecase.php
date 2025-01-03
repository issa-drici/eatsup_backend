<?php

namespace App\Application\Usecases\MenuItems;

use Illuminate\Support\Facades\Auth;
use App\Domain\Repositories\MenuItemRepositoryInterface;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Exceptions\UnauthorizedException;

class CountMenuItemsByMenuIdUsecase
{
    public function __construct(
        private MenuItemRepositoryInterface $menuItemRepository,
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

        // Retourner le nombre d'items
        return $this->menuItemRepository->findByMenuIdAndCount($menuId);
    }
}
