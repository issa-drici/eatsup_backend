<?php

namespace App\Http\Controllers\Restaurant;

use App\Application\Usecases\Restaurant\UpdateRestaurantUsecase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateRestaurantController extends Controller
{
    public function __construct(
        private UpdateRestaurantUsecase $updateRestaurantUsecase
    ) {
    }

    public function __invoke(string $restaurantId, Request $request)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|nullable|string|max:255',
            'phone' => 'sometimes|nullable|string|max:50',
            'logo' => 'sometimes|nullable|file|image|max:2048',
            'logo_url' => 'sometimes|nullable|string',
            'remove_logo' => 'sometimes|nullable|string',
            'social_links' => ['sometimes', 'nullable', function ($attribute, $value, $fail) {
                if ($value && !is_array(json_decode($value, true))) {
                    $fail('Le champ social_links doit être un JSON valide.');
                }
            }],
            'google_info' => ['sometimes', 'nullable', function ($attribute, $value, $fail) {
                if ($value && !is_array(json_decode($value, true))) {
                    $fail('Le champ google_info doit être un JSON valide.');
                }
            }],
        ]);

        // Décoder les champs JSON
        if (isset($data['social_links'])) {
            $data['social_links'] = json_decode($data['social_links'], true) ?? [];
        }
        if (isset($data['google_info'])) {
            $data['google_info'] = json_decode($data['google_info'], true) ?? [];
        }

        $restaurant = $this->updateRestaurantUsecase->execute($restaurantId, $data);

        return response()->json([
            'message' => 'Restaurant updated successfully',
            'data' => [
                'id' => $restaurant->getId(),
                'name' => $restaurant->getName(),
                'address' => $restaurant->getAddress(),
                'phone' => $restaurant->getPhone(),
                'logo_id' => $restaurant->getLogoId(),
                'social_links' => $restaurant->getSocialLinks(),
                'google_info' => $restaurant->getGoogleInfo(),
            ]
        ]);
    }
} 