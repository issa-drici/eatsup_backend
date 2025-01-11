<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\QrCodeSessionRepositoryInterface;
use App\Infrastructure\Models\QrCodeSessionModel;
use App\Domain\Entities\QrCodeSession;

class EloquentQrCodeSessionRepository implements QrCodeSessionRepositoryInterface
{
    public function countByRestaurantId(string $restaurantId): int
    {
        return QrCodeSessionModel::join('qr_codes', 'qr_code_sessions.qr_code_id', '=', 'qr_codes.id')
            ->where('qr_codes.restaurant_id', $restaurantId)
            ->count();
    }

    public function create(QrCodeSession $qrCodeSession): QrCodeSession
    {
        $model = QrCodeSessionModel::create([
            'id' => $qrCodeSession->getId(),
            'qr_code_id' => $qrCodeSession->getQrCodeId(),
            'scanned_at' => $qrCodeSession->getScannedAt(),
            'ip_address' => $qrCodeSession->getIpAddress(),
            'user_agent' => $qrCodeSession->getUserAgent(),
            'location' => $qrCodeSession->getLocation()
        ]);

        return $this->toDomainEntity($model);
    }

    public function findRecentByAttributes(string $qrCodeId, string $ipAddress, string $userAgent, int $minutes): ?QrCodeSession
    {
        $model = QrCodeSessionModel::where('qr_code_id', $qrCodeId)
            ->where('ip_address', $ipAddress)
            ->where('user_agent', $userAgent)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->first();

        return $model ? $this->toDomainEntity($model) : null;
    }

    private function toDomainEntity(QrCodeSessionModel $model): QrCodeSession
    {
        return new QrCodeSession(
            id: $model->id,
            qrCodeId: $model->qr_code_id,
            scannedAt: $model->scanned_at->format('Y-m-d H:i:s'),
            ipAddress: $model->ip_address,
            userAgent: $model->user_agent,
            location: $model->location,
            createdAt: $model->created_at->format('Y-m-d H:i:s'),
            updatedAt: $model->updated_at->format('Y-m-d H:i:s')
        );
    }
} 