<?php

namespace App\Domain\Entities;

class MenuCategory
{
    private string $id;
    private string $menuId;
    private array $name;
    private ?array $description;
    private int $sortOrder;

    public function __construct(
        string $id,
        string $menuId,
        array $name,
        ?array $description,
        int $sortOrder
    ) {
        $this->id = $id;
        $this->menuId = $menuId;
        $this->name = $name;
        $this->description = $description;
        $this->sortOrder = $sortOrder;
    }

    // --- Getters ---
    public function getId(): string
    {
        return $this->id;
    }
    public function getMenuId(): string
    {
        return $this->menuId;
    }
    public function getName(): array
    {
        return $this->name;
    }
    public function getDescription(): ?array
    {
        return $this->description;
    }
    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    // --- Setters ---
    public function setName(array $name): void
    {
        $this->name = $name;
    }
    public function setDescription(?array $description): void
    {
        $this->description = $description;
    }
    public function setSortOrder(int $sortOrder): void
    {
        $this->sortOrder = $sortOrder;
    }
}
