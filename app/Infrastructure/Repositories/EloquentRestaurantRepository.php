<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Restaurant;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Infrastructure\Models\RestaurantModel;

class EloquentRestaurantRepository implements RestaurantRepositoryInterface
{
    public function create(Restaurant $restaurant): Restaurant
    {
        $model = RestaurantModel::create([
            'id'          => $restaurant->getId(),
            'owner_id'    => $restaurant->getOwnerId(),
            'name'        => $restaurant->getName(),
            'address'     => $restaurant->getAddress(),
            'phone'       => $restaurant->getPhone(),
        ]);
        return $this->toDomainEntity($model);
    }

    public function findByOwnerId(string $ownerId): ?Restaurant
    {
        $model = RestaurantModel::where('owner_id', $ownerId)->first();
        if (!$model) {
            return null;
        }

        return new Restaurant(
            id: $model->id,
            ownerId: $model->owner_id,
            name: $model->name,
            address: $model->address,
            phone: $model->phone
        );
    }

    private function toDomainEntity(RestaurantModel $model): Restaurant
    {
        return new Restaurant(
            id: $model->id,
            ownerId: $model->owner_id,
            name: $model->name,
            address: $model->address,
            phone: $model->phone
        );
    }
}
