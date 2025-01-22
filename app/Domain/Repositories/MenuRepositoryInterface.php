<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Menu; // entité domain "Menu"

interface MenuRepositoryInterface
{
    public function create(Menu $menu): Menu;
    public function update(Menu $menu): Menu;
    public function findById(string $id): ?Menu;
    public function findByRestaurantId(string $restaurantId): array;
    public function delete(string $id): void;
}