<?php

namespace App\Application\Usecases\Website;

use Illuminate\Support\Facades\Auth;
use App\Domain\Repositories\WebsiteRepositoryInterface;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Domain\Repositories\FileRepositoryInterface;
use App\Exceptions\UnauthorizedException;
use App\Application\DTOs\WebsiteDTO;

class FindWebsiteByRestaurantIdUsecase
{
    public function __construct(
        private WebsiteRepositoryInterface $websiteRepository,
        private RestaurantRepositoryInterface $restaurantRepository,
        private FileRepositoryInterface $fileRepository
    ) {}

    public function execute(string $restaurantId): WebsiteDTO
    {
        // 1. Vérifier l'authentification
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }

        // 2. Vérifier les permissions
        if (!in_array($user->role, ['admin', 'franchise_manager'])) {
            $restaurant = $this->restaurantRepository->findByOwnerId($user->id);
            if (!$restaurant || $restaurant->getId() !== $restaurantId) {
                throw new UnauthorizedException("You do not have permission to view this website.");
            }
        }
        // 3. Récupérer le site web
        $website = $this->websiteRepository->findByRestaurantId($restaurantId);
        if (!$website) {
            throw new \Exception("Website not found for this restaurant.");
        }

        // 4. Récupérer l'URL du fichier si un ID est présent
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

        return new WebsiteDTO(
            id: $website->getId(),
            restaurantId: $website->getRestaurantId(),
            menuId: $website->getMenuId(),
            domain: $website->getDomain(),
            title: $website->getTitle(),
            description: $website->getDescription(),
            presentationImage: $presentationImage,
            openingHours: $website->getOpeningHours(),
            themeConfig: $website->getThemeConfig(),
            restaurant: [
                'name' => $restaurant->getName(),
                'address' => $restaurant->getAddress(),
                'phone' => $restaurant->getPhone(),
                'logo_id' => $restaurant->getLogoId(),
            ],
        );
    }
}
