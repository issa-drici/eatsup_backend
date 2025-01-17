<?php

namespace App\Domain\Entities;

class Website
{
    public function __construct(
        private string $id,
        private string $restaurantId,
        private array $title,
        private ?array $description,
        private ?array $themeConfig,
        private ?string $domain,
        private ?string $presentationImageId,
        private ?array $openingHours,
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

    public function getTitle(): array
    {
        return $this->title;
    }

    public function getDescription(): ?array
    {
        return $this->description;
    }

    public function getThemeConfig(): ?array
    {
        return $this->themeConfig;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function getPresentationImageId(): ?string
    {
        return $this->presentationImageId;
    }

    public function getOpeningHours(): ?array
    {
        return $this->openingHours;
    }

    public function setTitle(array $title): void
    {
        $this->title = $title;
    }

    public function setDescription(?array $description): void
    {
        $this->description = $description;
    }

    public function setThemeConfig(?array $themeConfig): void
    {
        $this->themeConfig = $themeConfig;
    }

    public function setDomain(?string $domain): void
    {
        $this->domain = $domain;
    }

    public function setPresentationImageId(?string $presentationImageId): void
    {
        $this->presentationImageId = $presentationImageId;
    }

    public function setOpeningHours(?array $openingHours): void
    {
        $this->openingHours = $openingHours;
    }
} 