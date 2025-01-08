<?php

namespace App\Domain\Entities;

class QrCode
{
    public function __construct(
        private string $id,
        private ?string $restaurantId,
        private ?string $menuId,
        private ?string $qrType,
        private ?string $label,
        private string $status
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getRestaurantId(): string
    {
        return $this->restaurantId;
    }

    public function setRestaurantId(string $restaurantId): void
    {
        $this->restaurantId = $restaurantId;
    }

    public function getMenuId(): ?string
    {
        return $this->menuId;
    }

    public function setMenuId(?string $menuId): void
    {
        $this->menuId = $menuId;
    }

    public function getQrType(): string
    {
        return $this->qrType;
    }

    public function setQrType(string $qrType): void
    {
        $this->qrType = $qrType;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
} 