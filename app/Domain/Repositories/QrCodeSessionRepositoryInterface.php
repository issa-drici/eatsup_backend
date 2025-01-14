<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\QrCodeSession;

interface QrCodeSessionRepositoryInterface
{
    public function create(QrCodeSession $qrCodeSession): QrCodeSession;
    public function countByRestaurantId(string $restaurantId): int;
    public function findRecentByAttributes(string $qrCodeId, string $ipAddress, string $userAgent, int $seconds): ?QrCodeSession;
} 