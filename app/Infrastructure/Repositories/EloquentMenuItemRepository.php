<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\MenuItem;
use App\Domain\Repositories\MenuItemRepositoryInterface;
use App\Infrastructure\Models\MenuItemModel;
use Illuminate\Support\Facades\DB;

class EloquentMenuItemRepository implements MenuItemRepositoryInterface
{
    public function create(MenuItem $menuItem): MenuItem
    {
        $model = MenuItemModel::create([
            'id' => $menuItem->getId(),
            'category_id' => $menuItem->getCategoryId(),
            'name' => $menuItem->getName(),
            'description' => $menuItem->getDescription(),
            'price' => $menuItem->getPrice(),
            'allergens' => $menuItem->getAllergens(),
            'images' => $menuItem->getImages(),
            'is_active' => $menuItem->isActive(),
            'sort_order' => $menuItem->getSortOrder()
        ]);

        return $this->toEntity($model);
    }

    public function update(MenuItem $menuItem): MenuItem
    {
        $model = MenuItemModel::findOrFail($menuItem->getId());
        
        $model->update([
            'name' => $menuItem->getName(),
            'description' => $menuItem->getDescription(),
            'price' => $menuItem->getPrice(),
            'allergens' => $menuItem->getAllergens(),
            'images' => $menuItem->getImages(),
            'is_active' => $menuItem->isActive(),
            'sort_order' => $menuItem->getSortOrder()
        ]);

        return $this->toEntity($model);
    }

    public function findById(string $id): ?MenuItem
    {
        $model = MenuItemModel::find($id);
        
        if (!$model) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function findByMenuIdAndCount(string $menuId): int
    {
        return DB::table('menu_items')
            ->join('menu_categories', 'menu_items.category_id', '=', 'menu_categories.id')
            ->where('menu_categories.menu_id', $menuId)
            ->count();
    }

    public function findAllByMenuCategoryId(string $menuCategoryId): array
    {
        $models = MenuItemModel::where('category_id', $menuCategoryId)
            ->orderBy('sort_order')
            ->get();
            
        return $models->map(fn($model) => $this->toEntity($model))->all();
    }

    private function toEntity(MenuItemModel $model): MenuItem
    {
        return new MenuItem(
            id: $model->id,
            categoryId: $model->category_id,
            name: $model->name,
            description: $model->description,
            price: $model->price,
            allergens: $model->allergens,
            images: $model->images,
            isActive: $model->is_active,
            sortOrder: $model->sort_order
        );
    }
}
