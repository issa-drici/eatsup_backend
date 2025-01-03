<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Restaurant;

interface RestaurantRepositoryInterface
{
    public function create(Restaurant $restaurant): Restaurant;
    public function findByOwnerId(string $ownerId): ?Restaurant;
}
