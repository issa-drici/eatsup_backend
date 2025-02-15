<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\MenuCategory;
use App\Domain\Repositories\MenuCategoryRepositoryInterface;
use App\Infrastructure\Models\MenuCategoryModel;
use Illuminate\Support\Facades\DB;

class EloquentMenuCategoryRepository implements MenuCategoryRepositoryInterface
{
    public function create(MenuCategory $menuCategory): MenuCategory
    {
        $model = MenuCategoryModel::create([
            'id'          => $menuCategory->getId(),
            'menu_id'     => $menuCategory->getMenuId(),
            'name'        => $menuCategory->getName(),
            'description' => $menuCategory->getDescription(),
            'sort_order'  => $menuCategory->getSortOrder(),
        ]);
        return $this->toDomainEntity($model);
    }

    public function update(MenuCategory $menuCategory): MenuCategory
    {
        $model = MenuCategoryModel::find($menuCategory->getId());
        if (!$model) {
            throw new \Exception("MenuCategory not found");
        }

        $model->update([
            'name'        => $menuCategory->getName(),
            'description' => $menuCategory->getDescription(),
            'sort_order'  => $menuCategory->getSortOrder(),
        ]);
        return $this->toDomainEntity($model);
    }

    public function findById(string $id): ?MenuCategory
    {
        $model = MenuCategoryModel::find($id);

        if (!$model) {
            return null;
        }

        return $this->toDomainEntity($model);
    }

    public function countByMenuId(string $menuId): int
    {
        return MenuCategoryModel::where('menu_id', $menuId)->count();
    }

    public function findAllByMenuId(string $menuId): array
    {
        $models = MenuCategoryModel::where('menu_id', $menuId)
            ->orderBy('sort_order')
            ->get();

        return $models->map(function ($model) {
            return $this->toDomainEntity($model);
        })->all();
    }

    public function getMaxSortOrderByMenuId(string $menuId): int
    {
        $maxSortOrder = MenuCategoryModel::where('menu_id', $menuId)
            ->max('sort_order');

        return $maxSortOrder ?? 0; // Retourne 0 si aucune catégorie n'existe
    }

    public function delete(string $id): void
    {
        $model = MenuCategoryModel::findOrFail($id);
        $model->delete();
    }

    public function findPreviousCategoryInMenu(string $menuId, int $currentSortOrder): ?MenuCategory
    {
        $model = MenuCategoryModel::where('menu_id', $menuId)
            ->where('sort_order', '<', $currentSortOrder)
            ->orderBy('sort_order', 'desc')
            ->first();

        return $model ? $this->toDomainEntity($model) : null;
    }

    public function findNextCategoryInMenu(string $menuId, int $currentSortOrder): ?MenuCategory
    {
        $model = MenuCategoryModel::where('menu_id', $menuId)
            ->where('sort_order', '>', $currentSortOrder)
            ->orderBy('sort_order', 'asc')
            ->first();

        return $model ? $this->toDomainEntity($model) : null;
    }

    private function toDomainEntity(MenuCategoryModel $model): MenuCategory
    {
        return new MenuCategory(
            id: $model->id,
            menuId: $model->menu_id,
            name: $model->name,
            description: $model->description,
            sortOrder: $model->sort_order
        );
    }

    public function countByRestaurantId(string $restaurantId): int
    {
        return DB::table('menu_categories')
            ->join('menus', 'menu_categories.menu_id', '=', 'menus.id')
            ->where('menus.restaurant_id', $restaurantId)
            ->count();
    }
}
