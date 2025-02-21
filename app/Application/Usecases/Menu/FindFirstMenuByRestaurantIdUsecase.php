<?php

namespace App\Application\Usecases\Menu;

use App\Domain\Repositories\MenuRepositoryInterface;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Domain\Repositories\FileRepositoryInterface;
use App\Application\DTOs\MenuWithRestaurantDTO;

class FindFirstMenuByRestaurantIdUsecase
{
    public function __construct(
        private MenuRepositoryInterface $menuRepository,
        private RestaurantRepositoryInterface $restaurantRepository,
        private FileRepositoryInterface $fileRepository
    ) {}

    public function execute(string $restaurantId): MenuWithRestaurantDTO
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

        // 3. Récupérer le fichier logo si présent
        $logo = null;
        if ($restaurant->getLogoId()) {
            $file = $this->fileRepository->findById($restaurant->getLogoId());
            if ($file) {
                $logo = [
                    'id' => $file->getId(),
                    'url' => $file->getUrl()
                ];
            }
        }

        // 4. Retourner le premier menu avec les informations du restaurant
        $firstMenu = $menus[0];
        return new MenuWithRestaurantDTO(
            id: $firstMenu->getId(),
            name: $firstMenu->getName(),
            status: $firstMenu->getStatus(),
            banners: $firstMenu->getBanners(),
            restaurant: [
                'id' => $restaurant->getId(),
                'name' => $restaurant->getName(),
                'address' => $restaurant->getAddress(),
                'phone' => $restaurant->getPhone(),
                'logo' => $logo,
                'postal_code' => $restaurant->getPostalCode(),
                'city' => $restaurant->getCity(),
                'city_slug' => $restaurant->getCitySlug(),
                'type_slug' => $restaurant->getTypeSlug(),
                'name_slug' => $restaurant->getNameSlug()
            ]
        );
    }
}
