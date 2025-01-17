<?php

namespace App\Http\Controllers\Restaurant;

use App\Application\Usecases\Restaurant\FindRestaurantByIdUsecase;
use App\Http\Controllers\Controller;

class FindRestaurantByIdController extends Controller
{
    public function __construct(
        private FindRestaurantByIdUsecase $findRestaurantByIdUsecase
    ) {
    }

    public function __invoke(string $restaurantId)
    {
        $restaurant = $this->findRestaurantByIdUsecase->execute($restaurantId);

        return response()->json([
            'message' => 'Restaurant retrieved successfully',
            'data' => [
                'id' => $restaurant->id,
                'name' => $restaurant->name,
                'address' => $restaurant->address,
                'phone' => $restaurant->phone,
                'logo' => $restaurant->logo,
                'postal_code' => $restaurant->postal_code,
                'city' => $restaurant->city,
                'city_slug' => $restaurant->city_slug,
                'name_slug' => $restaurant->name_slug,
                'type_slug' => $restaurant->type_slug,
                'social_links' => $restaurant->social_links,
                'google_info' => $restaurant->google_info,
                'owner' => $restaurant->owner
            ]
        ]);
    }
} 