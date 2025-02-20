<?php

namespace App\Application\DTOs;

class MenuWithRestaurantDTO
{
    public function __construct(
        private string $id,
        private array $name,
        private string $status,
        private ?array $banners,
        private array $restaurant
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): array
    {
        return $this->name;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getBanners(): ?array
    {
        return $this->banners;
    }

    public function getRestaurant(): array
    {
        return $this->restaurant;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'banners' => $this->banners,
            'restaurant' => $this->restaurant
        ];
    }
}
