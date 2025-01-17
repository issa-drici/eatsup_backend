<?php

namespace Database\Factories;

use App\Infrastructure\Models\RestaurantModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RestaurantFactory extends Factory
{
    protected $model = RestaurantModel::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'name_slug' => Str::slug(fake()->company()),
            'address' => fake()->address(),
            'postal_code' => fake()->postcode(),
            'city' => fake()->city(),
            'city_slug' => Str::slug(fake()->city()),
            'type_slug' => fake()->randomElement(['restaurant', 'cafe', 'bar', 'bistrot']),
            'phone' => fake()->phoneNumber(),
        ];
    }
}
