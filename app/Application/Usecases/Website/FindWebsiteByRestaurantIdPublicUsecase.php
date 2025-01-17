<?php

namespace App\Application\Usecases\Website;

use App\Domain\Repositories\WebsiteRepositoryInterface;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Domain\Repositories\FileRepositoryInterface;
use App\Application\DTOs\WebsiteDTO;

class FindWebsiteByRestaurantIdPublicUsecase
{
    public function __construct(
        private WebsiteRepositoryInterface $websiteRepository,
        private RestaurantRepositoryInterface $restaurantRepository,
        private FileRepositoryInterface $fileRepository
    ) {
    }

    public function execute(string $restaurantId): WebsiteDTO
    {
        // 1. Récupérer le site web
        $website = $this->websiteRepository->findByRestaurantId($restaurantId);
        if (!$website) {
            throw new \Exception("Website not found for this restaurant.");
        }

        // 2. Récupérer le restaurant
        $restaurant = $this->restaurantRepository->findById($restaurantId);
        if (!$restaurant) {
            throw new \Exception("Restaurant not found.");
        }

        // 3. Récupérer l'image de présentation si elle existe
        $presentationImage = null;
        if ($website->getPresentationImageId()) {
            $file = $this->fileRepository->findById($website->getPresentationImageId());
            if ($file) {
                $presentationImage = [
                    'id' => $file->getId(),
                    'url' => $file->getUrl()
                ];
            }
        }

        // 4. Créer et retourner le DTO
        return new WebsiteDTO(
            id: $website->getId(),
            restaurantId: $website->getRestaurantId(),
            domain: $website->getDomain(),
            title: $website->getTitle(),
            description: $website->getDescription(),
            presentationImage: $presentationImage,
            openingHours: $website->getOpeningHours(),
            themeConfig: $website->getThemeConfig(),
            restaurantName: $restaurant->getName(),
            restaurantAddress: $restaurant->getAddress(),
            restaurantPhone: $restaurant->getPhone(),
            restaurantLogoId: $restaurant->getLogoId(),
        );
    }
} 