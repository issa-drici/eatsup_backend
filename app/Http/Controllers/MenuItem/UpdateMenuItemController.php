<?php

namespace App\Http\Controllers\MenuItem;

use App\Application\Usecases\MenuItem\UpdateMenuItemUsecase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateMenuItemController extends Controller
{
    public function __construct(
        private UpdateMenuItemUsecase $updateMenuItemUsecase
    ) {}

    public function __invoke(string $menuItemId, Request $request)
    {
        $data = $request->validate([
            'name'        => ['sometimes', function ($attribute, $value, $fail) {
                if ($value && !is_array(json_decode($value, true))) {
                    $fail('Le champ name doit être un JSON valide.');
                }
            }],
            'description' => ['nullable', function ($attribute, $value, $fail) {
                if ($value && !is_array(json_decode($value, true))) {
                    $fail('Le champ description doit être un JSON valide.');
                }
            }],
            'price'      => 'sometimes|numeric',
            'allergens'  => 'nullable|array',
            'images'     => 'nullable|array',
            'images.*'   => 'file|image|max:2048',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'string|uuid',
            'sort_order' => 'nullable|integer',
            'is_active'  => 'nullable|accepted|in:1,true,on,yes'
        ]);

        // Décoder les champs JSON
        if (isset($data['name'])) {
            $data['name'] = json_decode($data['name'], true);
        }
        if (isset($data['description'])) {
            $data['description'] = json_decode($data['description'], true);
        }

        // Convertir is_active en booléen
        if (isset($data['is_active'])) {
            $data['is_active'] = filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN);
        }

        $menuItem = $this->updateMenuItemUsecase->execute($menuItemId, $data);

        return response()->json([
            'message' => 'Menu item updated successfully',
            'data'    => [
                'id'          => $menuItem->getId(),
                'category_id' => $menuItem->getCategoryId(),
                'name'        => $menuItem->getName(),
                'description' => $menuItem->getDescription(),
                'price'       => $menuItem->getPrice(),
                'allergens'   => $menuItem->getAllergens(),
                'images'      => $menuItem->getImages(),
                'sort_order'  => $menuItem->getSortOrder(),
                'is_active'   => $menuItem->isActive()
            ]
        ]);
    }
}
