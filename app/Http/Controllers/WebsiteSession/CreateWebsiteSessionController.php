<?php

namespace App\Http\Controllers\WebsiteSession;

use App\Application\Usecases\WebsiteSession\CreateWebsiteSessionUsecase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CreateWebsiteSessionController extends Controller
{
    public function __construct(
        private CreateWebsiteSessionUsecase $createWebsiteSessionUsecase
    ) {
    }

    public function __invoke(string $websiteId, Request $request)
    {
        $data = [
            'website_id' => $websiteId,
            'ip_address' => $request->input('ip_address') ?? $request->ip(),
            'user_agent' => $request->userAgent(),
            'location' => $request->input('location'),
        ];

        $session = $this->createWebsiteSessionUsecase->execute($data);

        return response()->json([
            'message' => 'Website session created successfully',
            'data' => [
                'id' => $session->getId(),
                'website_id' => $session->getWebsiteId(),
                'visited_at' => $session->getVisitedAt(),
                'ip_address' => $session->getIpAddress(),
                'user_agent' => $session->getUserAgent(),
                'location' => $session->getLocation(),
            ]
        ], 201);
    }
} 