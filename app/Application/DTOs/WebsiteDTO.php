<?php

namespace App\Application\DTOs;

class WebsiteDTO
{
    public function __construct(
        public string $id,
        public ?string $menuId,
        public ?string $domain,
        public ?array $title,
        public ?array $description,
        public ?array $presentationImage,
        public ?array $openingHours,
        public ?array $themeConfig,
        public array $restaurant = [],
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getMenuId(): ?string
    {
        return $this->menuId;
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

    public function getRestaurant(): array
    {
        return $this->restaurant;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'menu_id' => $this->menuId,
            'domain' => $this->domain,
            'title' => $this->title,
            'description' => $this->description,
            'presentation_image' => $this->presentationImage,
            'opening_hours' => $this->openingHours,
            'theme_config' => $this->themeConfig,
            'restaurant' => $this->restaurant,
        ];
    }
}
