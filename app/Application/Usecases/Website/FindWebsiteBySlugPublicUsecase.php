<?php

namespace App\Application\Usecases\Website;

use App\Domain\Repositories\WebsiteRepositoryInterface;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Domain\Repositories\FileRepositoryInterface;
use App\Application\DTOs\WebsiteDTO;
use App\Application\Usecases\WebsiteSession\CreateWebsiteSessionUsecase;
use Illuminate\Http\Request;

class FindWebsiteBySlugPublicUsecase
{
    public function __construct(
        private WebsiteRepositoryInterface $websiteRepository,
        private RestaurantRepositoryInterface $restaurantRepository,
        private FileRepositoryInterface $fileRepository,
        private CreateWebsiteSessionUsecase $createWebsiteSessionUsecase
    ) {}

    public function execute(Request $request, string $typeSlug, string $citySlug, string $nameSlug): WebsiteDTO
    {

        // 1. Trouver le restaurant correspondant aux slugs
        $restaurant = $this->restaurantRepository->findBySlug($typeSlug, $citySlug, $nameSlug);
        if (!$restaurant) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Restaurant non trouvé.");
        }

        // 2. Récupérer le site web
        $website = $this->websiteRepository->findByRestaurantId($restaurant->getId());
        if (!$website) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Site web non trouvé pour ce restaurant.");
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

        $data = [
            'website_id' => $website->getId(),
            'ip_address' => $request->input('ip_address') ?? $request->ip(),
            'user_agent' => $request->userAgent(),
            'location' => $request->input('location'),
        ];

        $session = $this->createWebsiteSessionUsecase->execute($data);

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
