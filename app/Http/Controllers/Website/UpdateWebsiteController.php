<?php

namespace App\Http\Controllers\Website;

use App\Application\Usecases\Website\UpdateWebsiteUsecase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateWebsiteController extends Controller
{
    public function __construct(
        private UpdateWebsiteUsecase $updateWebsiteUsecase
    ) {
    }

    public function __invoke(string $restaurantId, Request $request)
    {
        $data = $request->validate([
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'domain' => 'nullable|string',
            'presentation_image' => 'nullable|file|image|max:2048',
            'remove_presentation_image' => 'nullable|string|in:true',
            'opening_hours' => 'nullable|string',
            'theme_config' => 'nullable|string',
        ]);

        // Décoder les champs JSON
        if (isset($data['title'])) {
            $data['title'] = json_decode($data['title'], true) ?? [];
        }
        if (isset($data['description'])) {
            $data['description'] = json_decode($data['description'], true) ?? [];
        }
        if (isset($data['opening_hours'])) {
            $data['opening_hours'] = json_decode($data['opening_hours'], true) ?? [];
        }
        if (isset($data['theme_config'])) {
            $data['theme_config'] = json_decode($data['theme_config'], true) ?? [];
        }

        $website = $this->updateWebsiteUsecase->execute($restaurantId, $data);

        return response()->json([
            'message' => 'Website updated successfully',
            'data' => [
                'id' => $website->getId(),
                'restaurant_id' => $website->getRestaurantId(),
                'domain' => $website->getDomain(),
                'title' => $website->getTitle(),
                'description' => $website->getDescription(),
                'presentation_image_id' => $website->getPresentationImageId(),
                'opening_hours' => $website->getOpeningHours(),
                'theme_config' => $website->getThemeConfig(),
            ]
        ]);
    }
} 