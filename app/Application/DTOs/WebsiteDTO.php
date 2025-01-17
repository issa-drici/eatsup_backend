<?php

namespace App\Application\DTOs;

class WebsiteDTO
{
    public function __construct(
        private string $id,
        private string $restaurantId,
        private ?string $domain,
        private array $title,
        private ?array $description,
        private ?array $presentationImage,
        private ?array $openingHours,
        private ?array $themeConfig,
        // Restaurant info
        private string $restaurantName,
        private ?string $restaurantAddress,
        private ?string $restaurantPhone,
        private ?string $restaurantLogoId,
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

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function getTitle(): array
    {
        return $this->title;
    }

    public function getDescription(): ?array
    {
        return $this->description;
    }

    public function getPresentationImage(): ?array
    {
        return $this->presentationImage;
    }

    public function getOpeningHours(): ?array
    {
        return $this->openingHours;
    }

    public function getThemeConfig(): ?array
    {
        return $this->themeConfig;
    }

    public function getRestaurantName(): string
    {
        return $this->restaurantName;
    }

    public function getRestaurantAddress(): ?string
    {
        return $this->restaurantAddress;
    }

    public function getRestaurantPhone(): ?string
    {
        return $this->restaurantPhone;
    }

    public function getRestaurantLogoId(): ?string
    {
        return $this->restaurantLogoId;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'restaurant_id' => $this->restaurantId,
            'domain' => $this->domain,
            'title' => $this->title,
            'description' => $this->description,
            'presentation_image' => $this->presentationImage,
            'opening_hours' => $this->openingHours,
            'theme_config' => $this->themeConfig,
            'restaurant' => [
                'name' => $this->restaurantName,
                'address' => $this->restaurantAddress,
                'phone' => $this->restaurantPhone,
                'logo_id' => $this->restaurantLogoId,
            ],
        ];
    }
} 