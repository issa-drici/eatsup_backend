<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Menu;
use App\Domain\Repositories\MenuRepositoryInterface;
use App\Infrastructure\Models\MenuModel;

class EloquentMenuRepository implements MenuRepositoryInterface
{

    public function create(Menu $menu): Menu
    {
        $model = MenuModel::create([
            'id'          => $menu->getId(),
            'restaurant_id'    => $menu->getRestaurantId(),
            'name'        => $menu->getName(),
            'status'     => $menu->getStatus(),
            'banner'     => $menu->getBanner(), 
        ]);
        return $this->toDomainEntity($model);
    }

    public function findById(string $id): ?Menu
    {
        $model = MenuModel::find($id);
        if (!$model) {
            return null;
        }

        return $this->toDomainEntity($model);
    }

    public function findByRestaurantId(string $restaurantId): array
    {
        $models = MenuModel::where('restaurant_id', $restaurantId)->get();

        return $models->map(function ($model) {
            return $this->toDomainEntity($model);
        })->toArray();
    }
    

    private function toDomainEntity(MenuModel $model): Menu
    {
        return new Menu(
            id: $model->id,
            restaurantId: $model->restaurant_id,
            name: $model->name,
            status: $model->status,
            banner: $model->banner
        );
    }
}
