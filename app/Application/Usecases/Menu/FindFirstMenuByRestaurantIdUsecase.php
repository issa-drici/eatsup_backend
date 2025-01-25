<?php

namespace App\Application\Usecases\Menu;

use App\Domain\Repositories\MenuRepositoryInterface;
use App\Domain\Repositories\RestaurantRepositoryInterface;

class FindFirstMenuByRestaurantIdUsecase
{
    public function __construct(
        private MenuRepositoryInterface $menuRepository,
        private RestaurantRepositoryInterface $restaurantRepository
    ) {}

    public function execute(string $restaurantId): mixed
    {
        // 1. Vérifier si le restaurant existe
        $restaurant = $this->restaurantRepository->findById($restaurantId);
        if (!$restaurant) {
            throw new \Exception("Restaurant not found.");
        }

        // 2. Récupérer tous les menus du restaurant
        $menus = $this->menuRepository->findByRestaurantId($restaurantId);
        if (empty($menus)) {
            throw new \Exception("No menu found for this restaurant.");
        }

        // 3. Retourner le premier men
        return $menus[0];
    }
} 