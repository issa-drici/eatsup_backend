<?php

namespace App\Application\Usecases\Website;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Domain\Entities\Website;
use App\Domain\Repositories\WebsiteRepositoryInterface;
use App\Exceptions\UnauthorizedException;

class CreateWebsiteUsecase
{
    public function __construct(
        private WebsiteRepositoryInterface $websiteRepository
    ) {
    }

    public function execute(array $data): Website
    {
        // 1. Vérifier l'auth
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }

        // 2. Créer l'entité
        $website = new Website(
            id: Str::uuid()->toString(),
            restaurantId: $data['restaurant_id'],
            menuId: $data['menu_id'],
            title: $data['title'],
            description: $data['description'],
            themeConfig: $data['theme_config'],
            domain: null,
            presentationImageId: null,
            openingHours: null
        );

        // 3. Persister
        return $this->websiteRepository->create($website);
    }
} 