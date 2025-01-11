<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\QrCodeSession;

interface QrCodeSessionRepositoryInterface
{
    public function countByRestaurantId(string $restaurantId): int;
    public function create(QrCodeSession $qrCodeSession): QrCodeSession;
    public function findRecentByAttributes(string $qrCodeId, string $ipAddress, string $userAgent, int $minutes): ?QrCodeSession;
} 