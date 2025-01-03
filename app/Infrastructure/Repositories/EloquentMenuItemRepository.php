<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\MenuCategory;
use App\Domain\Repositories\MenuItemRepositoryInterface;
use App\Infrastructure\Models\MenuCategoryModel;
use Illuminate\Support\Facades\DB;

class EloquentMenuItemRepository implements MenuItemRepositoryInterface
{

    public function findByMenuIdAndCount(string $menuId): int
    {
        return DB::table('menu_items')
            ->join('menu_categories', 'menu_items.category_id', '=', 'menu_categories.id')
            ->where('menu_categories.menu_id', $menuId)
            ->count();
    }
}
