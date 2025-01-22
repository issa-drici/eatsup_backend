<?php

namespace App\Application\Usecases\WebsiteSession;

use Illuminate\Support\Str;
use App\Domain\Entities\WebsiteSession;
use App\Domain\Repositories\WebsiteSessionRepositoryInterface;

class CreateWebsiteSessionUsecase
{
    public function __construct(
        private WebsiteSessionRepositoryInterface $websiteSessionRepository
    ) {
    }

    public function execute(array $data): WebsiteSession
    {
        // 1. Vérifier les données requises
        if (empty($data['website_id'])) {
            throw new \InvalidArgumentException("Website ID is required.");
        }

        // 2. Vérifier si une session récente existe déjà
        $recentSession = $this->websiteSessionRepository->findRecentByAttributes(
            websiteId: $data['website_id'],
            ipAddress: $data['ip_address'],
            userAgent: $data['user_agent'],
            seconds: 120 // 5 minutes
        );

        // Si une session récente existe, la retourner
        if ($recentSession) {
            return $recentSession;
        }

        // 3. Créer l'entité
        $session = new WebsiteSession(
            id: Str::uuid()->toString(),
            websiteId: $data['website_id'],
            visitedAt: now()->format('Y-m-d H:i:s'),
            ipAddress: $data['ip_address'],
            userAgent: $data['user_agent'],
            location: $data['location'] ?? null,
            createdAt: now()->format('Y-m-d H:i:s'),
            updatedAt: now()->format('Y-m-d H:i:s')
        );

        // 4. Persister
        return $this->websiteSessionRepository->create($session);
    }
} 