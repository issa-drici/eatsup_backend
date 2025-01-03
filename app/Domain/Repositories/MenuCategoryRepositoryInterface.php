<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\MenuCategory;

interface MenuCategoryRepositoryInterface
{
    public function create(MenuCategory $menuCategory): MenuCategory;
    public function update(MenuCategory $menuCategory): MenuCategory;
    public function findById(string $id): ?MenuCategory;
    public function findByMenuIdAndCount(string $menuId): int;
}
