<?php

namespace App\Application\Usecases\Menu;

use Illuminate\Support\Facades\Auth;
use App\Application\Usecases\MenuCategory\CreateMenuCategoryUsecase;
use App\Application\Usecases\MenuItem\CreateMenuItemUsecase;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Exceptions\UnauthorizedException;

class AiMenuGeneratorUsecase
{
    public function __construct(
        private CreateMenuCategoryUsecase $createMenuCategoryUsecase,
        private CreateMenuItemUsecase $createMenuItemUsecase,
        private MenuRepositoryInterface $menuRepository,
        private RestaurantRepositoryInterface $restaurantRepository
    ) {}

    public function execute(string $restaurantId, string $menuId, array $aiMenuData): array
    {
        // 1. Vérifier l'authentification
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("Utilisateur non authentifié.");
        }

        // 2. Vérifier le rôle
        if (!in_array($user->role, ['admin', 'restaurant_owner', 'franchise_manager'])) {
            throw new UnauthorizedException("Utilisateur non autorisé à générer des menus.");
        }

        // 3. Récupérer le restaurant et vérifier les permissions
        $restaurant = $this->restaurantRepository->findById($restaurantId);
        if (!$restaurant) {
            throw new UnauthorizedException("Restaurant non trouvé.");
        }

        // 4. Vérifier que l'utilisateur est propriétaire du restaurant
        if ($restaurant->getOwnerId() !== $user->id) {
            throw new UnauthorizedException("Ce restaurant ne vous appartient pas.");
        }

        // 5. Vérifier que le menu existe et appartient au restaurant
        $menu = $this->menuRepository->findById($menuId);
        if (!$menu || $menu->getRestaurantId() !== $restaurant->getId()) {
            throw new UnauthorizedException("Ce menu n'appartient pas à votre restaurant.");
        }

        $createdCategories = [];
        $createdItems = [];

        // Traiter chaque catégorie
        foreach ($aiMenuData['categories'] as $categoryData) {
            // Créer la catégorie
            $category = $this->createMenuCategoryUsecase->execute([
                'menu_id' => $menuId,
                'name' => ['fr' => $categoryData['name']], // Format attendu par le use case de traduction
                'description' => $categoryData['description'] ? ['fr' => $categoryData['description']] : null,
                'position' => count($createdCategories) + 1
            ]);

            $createdCategories[] = $category;

            // Traiter chaque item de la catégorie
            foreach ($categoryData['items'] as $index => $itemData) {
                // Nettoyer le prix (enlever le symbole € et convertir en centimes)
                $priceInCents = $this->convertPriceToCents($itemData['price']);

                $item = $this->createMenuItemUsecase->execute([
                    'category_id' => $category->getId(), // Le use case attend 'category_id', pas 'menu_category_id'
                    'name' => ['fr' => $itemData['name']], // Format attendu par le use case de traduction
                    'description' => $itemData['description'] ? ['fr' => $itemData['description']] : null,
                    'price' => $priceInCents,
                    'is_active' => true
                ]);

                $createdItems[] = $item;
            }
        }

        return [
            'menu_id' => $menuId,
            'created_categories' => count($createdCategories),
            'created_items' => count($createdItems),
            'categories' => $createdCategories,
            'items' => $createdItems
        ];
    }

    /**
     * Convertit un prix string (ex: "16.80€" ou "16,80€") en centimes (int).
     * Stocke le prix en centimes dans la base.
     */
    private function convertPriceToCents(string $price): int
    {
        // Enlever le symbole € et les espaces
        $cleanPrice = str_replace(['€', ' '], '', $price);
        // Remplacer la virgule par un point si besoin
        $cleanPrice = str_replace(',', '.', $cleanPrice);
        // Convertir en float puis en centimes
        $priceInEuros = (float) $cleanPrice;
        return (int) round($priceInEuros);
    }
}
