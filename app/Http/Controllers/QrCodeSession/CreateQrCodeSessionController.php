<?php

namespace App\Http\Controllers\QrCodeSession;

use App\Application\Usecases\QrCodeSession\CreateQrCodeSessionUsecase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CreateQrCodeSessionController extends Controller
{
    public function __construct(
        private CreateQrCodeSessionUsecase $createQrCodeSessionUsecase
    ) {
    }

    public function __invoke(string $qrCodeId, Request $request)
    {
        $data = [
            'qr_code_id' => $qrCodeId,
            'ip_address' => $request->input('ip_address') ?? $request->ip(),
            'user_agent' => $request->userAgent(),
            'location' => $request->input('location'),
        ];

        $session = $this->createQrCodeSessionUsecase->execute($data);

        return response()->json([
            'message' => 'QR code session created successfully',
            'data' => [
                'id' => $session->getId(),
                'qr_code_id' => $session->getQrCodeId(),
                'scanned_at' => $session->getScannedAt(),
                'ip_address' => $session->getIpAddress(),
                'user_agent' => $session->getUserAgent(),
                'location' => $session->getLocation()
            ]
        ], 201);
    }
} 