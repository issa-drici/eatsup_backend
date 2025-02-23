<?php

namespace App\Application\Usecases\Website;

use App\Domain\Repositories\WebsiteRepositoryInterface;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Domain\Repositories\FileRepositoryInterface;
use App\Application\DTOs\WebsiteDTO;

class FindWebsiteBySlugPublicUsecase
{
    public function __construct(
        private WebsiteRepositoryInterface $websiteRepository,
        private RestaurantRepositoryInterface $restaurantRepository,
        private FileRepositoryInterface $fileRepository
    ) {
    }

    public function execute(string $typeSlug, string $citySlug, string $nameSlug): WebsiteDTO
    {

        // 1. Trouver le restaurant correspondant aux slugs
        $restaurant = $this->restaurantRepository->findBySlug($typeSlug, $citySlug, $nameSlug);
        if (!$restaurant) {
            throw new \Exception("Restaurant not found.");
        }

        // 2. Récupérer le site web
        $website = $this->websiteRepository->findByRestaurantId($restaurant->getId());
        if (!$website) {
            throw new \Exception("Website not found for this restaurant.");
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


        // 4. Récupérer le fichier logo si présent
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

        // 5. Créer et retourner le DTO
        return new WebsiteDTO(
            id: $website->getId(),
            menuId: $website->getMenuId(),
            domain: $website->getDomain(),
            title: $website->getTitle(),
            description: $website->getDescription(),
            presentationImage: $presentationImage,
            openingHours: $website->getOpeningHours(),
            themeConfig: $website->getThemeConfig(),
            restaurant: [
                'id' => $restaurant->getId(),
                'name' => $restaurant->getName(),
                'address' => $restaurant->getAddress(),
                'phone' => $restaurant->getPhone(),
                'postal_code' => $restaurant->getPostalCode(),
                'city' => $restaurant->getCity(),
                'logo' => $logo,
            ],
        );
    }
}
