<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\WebsiteSession;

interface WebsiteSessionRepositoryInterface
{
    public function create(WebsiteSession $websiteSession): WebsiteSession;
    public function countByRestaurantId(string $restaurantId): int;
    public function findRecentByAttributes(string $websiteId, string $ipAddress, string $userAgent, int $seconds, bool $forUpdate = false): ?WebsiteSession;
}
