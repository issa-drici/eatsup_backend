<?php

namespace App\Http\Controllers\Menu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class AiMenuGeneratorStatusController extends Controller
{
    public function __invoke(Request $request, string $jobId): JsonResponse
    {
        $cacheKey = "ai_menu_generation_{$jobId}";
        $status = Cache::get($cacheKey);

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'Job non trouvé ou expiré',
                'status' => 'not_found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $status
        ]);
    }
}
