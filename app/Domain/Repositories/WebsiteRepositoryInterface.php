<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Website;

interface WebsiteRepositoryInterface
{
    public function create(Website $website): Website;
    public function findById(string $id): ?Website;
    public function findByRestaurantId(string $restaurantId): ?Website;
    public function update(Website $website): Website;
    public function delete(string $id): void;
} 