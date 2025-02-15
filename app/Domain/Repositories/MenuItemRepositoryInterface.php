<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\MenuItem;

interface MenuItemRepositoryInterface
{
    public function create(MenuItem $menuItem): MenuItem;
    public function update(MenuItem $menuItem): MenuItem;
    public function findById(string $id): ?MenuItem;
    public function findByMenuIdAndCount(string $menuId): int;
    public function findAllByMenuCategoryId(string $menuCategoryId): array;
    public function getMaxSortOrderByCategoryId(string $categoryId): int;
    public function delete(string $id): void;
    public function countByCategoryId(string $categoryId): int;
    public function findPreviousItemInCategory(string $categoryId, int $currentSortOrder): ?MenuItem;
    public function findNextItemInCategory(string $categoryId, int $currentSortOrder): ?MenuItem;
    public function countByRestaurantId(string $restaurantId): int;
}
