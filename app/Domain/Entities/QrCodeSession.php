<?php

namespace App\Domain\Entities;

class QrCodeSession
{
    public function __construct(
        private string $id,
        private string $qrCodeId,
        private string $scannedAt,
        private string $ipAddress,
        private string $userAgent,
        private ?string $location,
        private string $createdAt,
        private string $updatedAt
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getQrCodeId(): string
    {
        return $this->qrCodeId;
    }

    public function getScannedAt(): string
    {
        return $this->scannedAt;
    }

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }
} 