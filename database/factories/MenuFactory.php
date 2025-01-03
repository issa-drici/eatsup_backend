<?php

namespace Database\Factories;

use App\Infrastructure\Models\MenuModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MenuFactory extends Factory
{
    protected $model = MenuModel::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid()->toString(),
            'restaurant_id' => Str::uuid()->toString(),
            'name' => ['en' => 'Sample Menu', 'fr' => 'Menu Exemple'],
            'status' => 'active',
            'banner' => null,
        ];
    }
}
