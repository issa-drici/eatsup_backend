<?php

namespace App\Domain\Entities;

class Menu
{
    private string $id;
    private string $restaurantId;
    private array $name;           // ex: { "fr": "Menu Principal", "en": "Main Menu" }
    private string $status;        // ex: 'draft', 'active'
    private ?array $banners;        // [{ "id": "uuid", "url": "..." }, ...]

    public function __construct(
        string $id,
        string $restaurantId,
        array $name,
        string $status,
        ?array $banners = null
    ) {
        $this->id = $id;
        $this->restaurantId = $restaurantId;
        $this->name = $name;
        $this->status = $status;
        $this->banners = $banners;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getRestaurantId(): string
    {
        return $this->restaurantId;
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

    public function setName(array $name): void
    {
        $this->name = $name;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setBanners(?array $banners): void
    {
        $this->banners = $banners;
    }
}
