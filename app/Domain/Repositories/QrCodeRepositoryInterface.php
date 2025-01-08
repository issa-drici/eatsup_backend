<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\QrCode;

interface QrCodeRepositoryInterface
{
    public function findById(string $id): ?QrCode;
    public function update(QrCode $qrCode): QrCode;
    public function findAllByRestaurantId(string $restaurantId): array;
} 