<?php

namespace App\Domain\Entities;

class MenuItem
{
    public function __construct(
        private string $id,
        private string $categoryId,
        private array $name,
        private ?array $description,
        private float $price,
        private ?string $allergens,
        private ?array $images,
        private bool $isActive,
        private int $sortOrder,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCategoryId(): string
    {
        return $this->categoryId;
    }

    public function getName(): array
    {
        return $this->name;
    }

    public function getDescription(): ?array
    {
        return $this->description;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getAllergens(): ?string
    {
        return $this->allergens;
    }

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setName(array $name): void
    {
        $this->name = $name;
    }

    public function setDescription(?array $description): void
    {
        $this->description = $description;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function setAllergens(?string $allergens): void
    {
        $this->allergens = $allergens;
    }

    public function setImages(?array $images): void
    {
        $this->images = $images;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function setSortOrder(int $sortOrder): void
    {
        $this->sortOrder = $sortOrder;
    }
} 