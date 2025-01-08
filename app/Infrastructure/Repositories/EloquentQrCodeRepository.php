<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\QrCode;
use App\Domain\Repositories\QrCodeRepositoryInterface;
use App\Infrastructure\Models\QrCodeModel;

class EloquentQrCodeRepository implements QrCodeRepositoryInterface
{
    public function findById(string $id): ?QrCode
    {
        $model = QrCodeModel::find($id);

        if (!$model) {
            return null;
        }

        return $this->toDomainEntity($model);
    }

    public function update(QrCode $qrCode): QrCode
    {

        $model = QrCodeModel::findOrFail($qrCode->getId());
        $model->update([
            'restaurant_id' => $qrCode->getRestaurantId(),
            'menu_id' => $qrCode->getMenuId(),
            'qr_type' => $qrCode->getQrType(),
            'label' => $qrCode->getLabel(),
            'status' => $qrCode->getStatus(),
        ]);

        return $this->toDomainEntity($model);
    }

    public function findAllByRestaurantId(string $restaurantId): array
    {
        $qrCodes = QrCodeModel::where('restaurant_id', $restaurantId)
            ->get();

        return $qrCodes->map(function ($model) {
            return $this->toDomainEntity($model);
        })->all();
    }

    private function toDomainEntity(QrCodeModel $model): QrCode
    {
        return new QrCode(
            id: $model->id,
            restaurantId: $model->restaurant_id,
            menuId: $model->menu_id,
            qrType: $model->qr_type,
            label: $model->label,
            status: $model->status
        );
    }
} 