<?php

namespace Database\Factories;

use App\Infrastructure\Models\MenuCategoryModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MenuCategoryFactory extends Factory
{
    protected $model = MenuCategoryModel::class;

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
