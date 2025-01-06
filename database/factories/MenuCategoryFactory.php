<?php

namespace Database\Factories;

use App\Infrastructure\Models\MenuCategoriesModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MenuCategoriesFactory extends Factory
{
    protected $model = MenuCategoriesModel::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid()->toString(),
            'menu_id' => Str::uuid()->toString(),
            'name' => ['en' => 'Sample Category', 'fr' => 'Catégorie Exemple'],
            'description' => ['en' => 'Sample Description', 'fr' => 'Description Exemple'],
            'sort_order' => 1,
        ];
    }
}
