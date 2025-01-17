<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Restaurant;
use App\Application\DTOs\RestaurantWithOwnerDTO;

interface RestaurantRepositoryInterface
{
    public function create(Restaurant $restaurant): Restaurant;
    public function findByOwnerId(string $ownerId): ?Restaurant;
    public function findAllWithoutQRCode(): array;
    public function findByIdWithOwner(string $id): ?RestaurantWithOwnerDTO;
    public function findAllWithOwners(): array;
    public function findAllWithOwnersPaginated(array $filters = [], int $page = 1, int $perPage = 10): array;
    public function findAllWithQrCodeCount(): array;
    public function update(Restaurant $restaurant): Restaurant;
    public function findById(string $id): ?Restaurant;
    public function findBySlug(string $typeSlug, string $citySlug, string $nameSlug): ?Restaurant;
}
