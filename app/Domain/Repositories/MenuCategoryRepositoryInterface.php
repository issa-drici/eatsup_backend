<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\MenuCategory;

interface MenuCategoryRepositoryInterface
{
    public function create(MenuCategory $menuCategory): MenuCategory;
    public function update(MenuCategory $menuCategory): MenuCategory;
    public function findById(string $id): ?MenuCategory;
    public function countByMenuId(string $menuId): int;
    public function findAllByMenuId(string $menuId): array;
    public function getMaxSortOrderByMenuId(string $menuId): int;
    public function delete(string $id): void;
    public function findPreviousCategoryInMenu(string $menuId, int $currentSortOrder): ?MenuCategory;
    public function findNextCategoryInMenu(string $menuId, int $currentSortOrder): ?MenuCategory;
}
