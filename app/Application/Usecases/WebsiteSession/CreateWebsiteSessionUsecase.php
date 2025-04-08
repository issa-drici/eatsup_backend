<?php

namespace App\Application\Usecases\WebsiteSession;

use Illuminate\Support\Str;
use App\Domain\Entities\WebsiteSession;
use App\Domain\Repositories\WebsiteSessionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\UniqueConstraintViolationException;

class CreateWebsiteSessionUsecase
{
    public function __construct(
        private WebsiteSessionRepositoryInterface $websiteSessionRepository
    ) {}

    public function execute(array $data): WebsiteSession
    {
        // 1. Vérifier les données requises
        if (empty($data['website_id'])) {
            throw new \InvalidArgumentException("Website ID is required.");
        }

        try {
            // 2. Utiliser une transaction pour éviter les doublons
            return DB::transaction(function () use ($data) {
                // Verrouiller la table pendant la vérification
                $recentSession = $this->findRecentSession($data, true);
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
            });
        } catch (UniqueConstraintViolationException $e) {
            // Si une session similaire existe déjà, on essaie de la retrouver
            $recentSession = $this->findRecentSession($data);
            if ($recentSession) {
                return $recentSession;
            }

            // Si on ne trouve pas la session, on propage l'exception
            throw $e;
        }
    }

    private function findRecentSession(array $data, bool $forUpdate = false): ?WebsiteSession
    {
        return $this->websiteSessionRepository->findRecentByAttributes(
            websiteId: $data['website_id'],
            ipAddress: $data['ip_address'],
            userAgent: $data['user_agent'],
            seconds: 120, // 2 minutes
            forUpdate: $forUpdate
        );
    }
}
