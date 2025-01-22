<?php

namespace App\Http\Controllers\Menu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Application\Usecases\Menu\UpdateMenuUsecase;

class UpdateMenuController extends Controller
{
    public function __construct(
        private UpdateMenuUsecase $updateMenuUsecase
    ) {}

    public function __invoke(string $restaurantId, string $menuId, Request $request)
    {
        $data = $request->validate([
            'name' => ['sometimes', function ($attribute, $value, $fail) {
                if ($value && !is_array(json_decode($value, true))) {
                    $fail('Le champ name doit être un JSON valide.');
                }
            }],
            'status' => 'sometimes|string|in:draft,published',
            'banners' => 'nullable|array',
            'banners.*' => [
                'file',
                'image',
                'max:92160',
            ],
            'remove_banners' => 'nullable|array',
            'remove_banners.*' => 'string|uuid',
        ]);

        // Décoder les champs JSON
        if (isset($data['name'])) {
            $data['name'] = json_decode($data['name'], true);
        }

        $menu = $this->updateMenuUsecase->execute($menuId, $data);

        return response()->json([
            'message' => 'Menu updated successfully',
            'data' => [
                'id' => $menu->getId(),
                'restaurant_id' => $menu->getRestaurantId(),
                'name' => $menu->getName(),
                'status' => $menu->getStatus(),
                'banners' => $menu->getBanners(),
            ]
        ]);
    }
} 