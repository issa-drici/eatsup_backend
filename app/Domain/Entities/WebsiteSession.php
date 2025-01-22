<?php

namespace App\Domain\Entities;

class WebsiteSession
{
    public function __construct(
        private string $id,
        private string $websiteId,
        private string $visitedAt,
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

    public function getWebsiteId(): string
    {
        return $this->websiteId;
    }

    public function getVisitedAt(): string
    {
        return $this->visitedAt;
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