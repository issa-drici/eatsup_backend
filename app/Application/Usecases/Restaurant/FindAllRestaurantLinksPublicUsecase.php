<?php

namespace App\Application\Usecases\Restaurant;

use App\Domain\Repositories\RestaurantRepositoryInterface;

class FindAllRestaurantLinksPublicUsecase
{
    private const BASE_URL = 'https://www.eatsup.fr';

    public function __construct(
        private RestaurantRepositoryInterface $restaurantRepository
    ) {
    }

    public function execute(): array
    {
        // 1. Récupérer tous les restaurants
        $restaurants = $this->restaurantRepository->findAll();

        // 2. Ne retourner que les URLs
        return array_map(function ($restaurant) {
            return sprintf(
                '%s/%s/%s/%s',
                self::BASE_URL,
                $restaurant->getTypeSlug(),
                $restaurant->getCitySlug(),
                $restaurant->getNameSlug()
            );
        }, $restaurants);
    }
}
