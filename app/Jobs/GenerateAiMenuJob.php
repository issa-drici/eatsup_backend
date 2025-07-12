<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Application\Usecases\Menu\AiMenuGeneratorUsecase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class GenerateAiMenuJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;

    public function __construct(
        private string $restaurantId,
        private string $menuId,
        private array $aiMenuData,
        private string $jobId,
        private ?string $userId // Peut être string ou int selon le type d'ID
    ) {}

    public function handle(AiMenuGeneratorUsecase $aiMenuGeneratorUsecase): void
    {
        $cacheKey = "ai_menu_generation_{$this->jobId}";

        // Log de début pour debug
        Log::info('AI menu generation job started', [
            'job_id' => $this->jobId,
            'restaurant_id' => $this->restaurantId,
            'menu_id' => $this->menuId,
            'user_id' => $this->userId
        ]);

        // Authentifier l'utilisateur pour le contexte du job
        if ($this->userId) {
            Auth::loginUsingId($this->userId);
            Log::info('User authenticated in job', ['user_id' => $this->userId]);
        }

        // Statut initial : processing
        Cache::put($cacheKey, [
            'status' => 'processing',
            'message' => 'Génération en cours...'
        ], 3600);

        try {
            Log::info('Starting AI menu generation usecase', [
                'job_id' => $this->jobId,
                'ai_menu_data' => $this->aiMenuData
            ]);

            $result = $aiMenuGeneratorUsecase->execute(
                $this->restaurantId,
                $this->menuId,
                $this->aiMenuData
            );

            // Marquer comme terminé avec succès
            Cache::put($cacheKey, [
                'status' => 'completed',
                'message' => 'Menu généré avec succès',
                'data' => $result,
                'categories_created' => $result['created_categories'],
                'items_created' => $result['created_items']
            ], 3600);

        } catch (\Exception $e) {
            // Log détaillé de l'erreur
            Log::error('AI menu generation failed - DETAILED ERROR', [
                'job_id' => $this->jobId,
                'restaurant_id' => $this->restaurantId,
                'menu_id' => $this->menuId,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_class' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);

            // Marquer comme échoué
            Cache::put($cacheKey, [
                'status' => 'failed',
                'message' => 'Erreur lors de la génération',
                'error' => $e->getMessage(),
                'error_details' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 3600);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $cacheKey = "ai_menu_generation_{$this->jobId}";

        Cache::put($cacheKey, [
            'status' => 'failed',
            'message' => 'Job échoué',
            'error' => $exception->getMessage(),
            'error_details' => [
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ]
        ], 3600);

        Log::error('AI menu generation job failed', [
            'job_id' => $this->jobId,
            'restaurant_id' => $this->restaurantId,
            'menu_id' => $this->menuId,
            'error' => $exception->getMessage()
        ]);
    }
}
