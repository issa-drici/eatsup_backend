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
            'id' => Str::uuid()->toString(),
            'owner_id' => Str::uuid()->toString(),
            'name' => 'Sample Restaurant',
            'address' => '123 Sample St.',
            'phone' => '1234567890',
            'logo_url' => null,
            'social_links' => null,
            'google_info' => null,
        ];
    }
}
