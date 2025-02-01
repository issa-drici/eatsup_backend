<?php

namespace App\Http\Controllers\MenuItem;

use App\Application\Usecases\MenuItem\CreateMenuItemUsecase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CreateMenuItemController extends Controller
{
    public function __construct(
        private CreateMenuItemUsecase $createMenuItemUsecase
    ) {
    }

    public function __invoke(string $menuCategoryId, Request $request)
    {

        $data = $request->validate([
            'name'        => ['required', function ($attribute, $value, $fail) {
                if ($value && !is_array(json_decode($value, true))) {
                    $fail('Le champ name doit être un JSON valide.');
                }
            }],
            'description' => ['nullable', function ($attribute, $value, $fail) {
                if ($value && !is_array(json_decode($value, true))) {
                    $fail('Le champ description doit être un JSON valide.');
                }
            }],
            'price'       => 'required|numeric|min:0',
            'allergens'   => 'nullable|string',
            'images'      => 'nullable|array',
            'images.*'    => 'nullable|file|image',
            'is_active'   => 'nullable|accepted|in:1,true,on,yes'
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

        // Ajouter le menuCategoryId aux données
        $data['category_id'] = $menuCategoryId;

        $menuItem = $this->createMenuItemUsecase->execute($data);

        return response()->json([
            'message' => 'Menu item created successfully',
            'data'    => [
                'id'          => $menuItem->getId(),
                'category_id' => $menuItem->getCategoryId(),
                'name'        => $menuItem->getName(),
                'description' => $menuItem->getDescription(),
                'price'       => $menuItem->getPrice(),
                'allergens'   => $menuItem->getAllergens(),
                'images'      => $menuItem->getImages(),
                'is_active'   => $menuItem->isActive(),
                'sort_order'  => $menuItem->getSortOrder(),
            ]
        ], 201);
    }
} 