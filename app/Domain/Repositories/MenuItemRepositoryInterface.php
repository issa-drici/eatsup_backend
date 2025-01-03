<?php

namespace App\Domain\Repositories;

interface MenuItemRepositoryInterface
{
    public function findByMenuIdAndCount(string $menuId): int;
}
