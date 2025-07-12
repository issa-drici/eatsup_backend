<?php

namespace App\Http\Controllers\Menu;

use App\Http\Controllers\Controller;
use App\Application\Usecases\Menu\AiMenuGeneratorUsecase;
use App\Exceptions\UnauthorizedException;
use App\Jobs\GenerateAiMenuJob;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AiMenuGeneratorController extends Controller
{
    public function __construct(
        private AiMenuGeneratorUsecase $aiMenuGeneratorUsecase
    ) {}

    public function __invoke(Request $request, string $restaurantId, string $menuId): JsonResponse
    {
        try {
            // Valider les données reçues
            $validated = $request->validate([
                'categories' => 'required|array',
                'categories.*.name' => 'required|string|max:255',
                'categories.*.description' => 'nullable|string',
                'categories.*.items' => 'required|array',
                'categories.*.items.*.name' => 'required|string|max:255',
                'categories.*.items.*.description' => 'nullable|string',
                'categories.*.items.*.price' => 'required|string|max:50',
            ]);

            $aiMenuData = ['categories' => $validated['categories']];
            $jobId = uniqid();
            $userId = Auth::id();

            // Lancer le job en arrière-plan avec l'id utilisateur
            GenerateAiMenuJob::dispatch($restaurantId, $menuId, $aiMenuData, $jobId, $userId);

            return response()->json([
                'success' => true,
                'message' => 'Génération de menu lancée en arrière-plan',
                'processing' => true,
                'job_id' => $jobId
            ], 202);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);

        } catch (UnauthorizedException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 403);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du menu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
