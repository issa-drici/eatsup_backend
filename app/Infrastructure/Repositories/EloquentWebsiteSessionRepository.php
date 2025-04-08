<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\WebsiteSessionRepositoryInterface;
use App\Infrastructure\Models\WebsiteSessionModel;
use App\Domain\Entities\WebsiteSession;

class EloquentWebsiteSessionRepository implements WebsiteSessionRepositoryInterface
{
    public function countByRestaurantId(string $restaurantId): int
    {
        return WebsiteSessionModel::join('websites', 'website_sessions.website_id', '=', 'websites.id')
            ->where('websites.restaurant_id', $restaurantId)
            ->count();
    }

    public function create(WebsiteSession $websiteSession): WebsiteSession
    {
        $model = WebsiteSessionModel::create([
            'id' => $websiteSession->getId(),
            'website_id' => $websiteSession->getWebsiteId(),
            'visited_at' => $websiteSession->getVisitedAt(),
            'ip_address' => $websiteSession->getIpAddress(),
            'user_agent' => $websiteSession->getUserAgent(),
            'location' => $websiteSession->getLocation()
        ]);

        return $this->toDomainEntity($model);
    }

    public function findRecentByAttributes(string $websiteId, string $ipAddress, string $userAgent, int $seconds, bool $forUpdate = false): ?WebsiteSession
    {
        $recentTime = now()->subSeconds($seconds);

        $query = WebsiteSessionModel::where('website_id', $websiteId)
            ->where('ip_address', $ipAddress)
            ->where('user_agent', $userAgent)
            ->where('created_at', '>=', $recentTime);

        if ($forUpdate) {
            $query->lockForUpdate();
        }

        $model = $query->first();

        return $model ? $this->toDomainEntity($model) : null;
    }

    private function toDomainEntity(WebsiteSessionModel $model): WebsiteSession
    {
        return new WebsiteSession(
            id: $model->id,
            websiteId: $model->website_id,
            visitedAt: $model->visited_at->format('Y-m-d H:i:s'),
            ipAddress: $model->ip_address,
            userAgent: $model->user_agent,
            location: $model->location,
            createdAt: $model->created_at->format('Y-m-d H:i:s'),
            updatedAt: $model->updated_at->format('Y-m-d H:i:s')
        );
    }
}
