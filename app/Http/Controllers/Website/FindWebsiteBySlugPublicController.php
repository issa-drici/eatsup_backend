<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Application\Usecases\Website\FindWebsiteBySlugPublicUsecase;

class FindWebsiteBySlugPublicController extends Controller
{
    public function __construct(
        private FindWebsiteBySlugPublicUsecase $findWebsiteBySlugPublicUsecase
    ) {
    }

    public function __invoke(string $typeSlug, string $citySlug, string $nameSlug)
    {
        $website = $this->findWebsiteBySlugPublicUsecase->execute($typeSlug, $citySlug, $nameSlug);

        return response()->json([
            'data' => [
                'id' => $website->getId(),
                'menu_id' => $website->getMenuId(),
                'domain' => $website->getDomain(),
                'title' => $website->getTitle(),
                'description' => $website->getDescription(),
                'presentation_image' => $website->getPresentationImage(),
                'opening_hours' => $website->getOpeningHours(),
                'theme_config' => $website->getThemeConfig(),
                'restaurant' => $website->getRestaurant(),
            ]
        ]);
    }
}
