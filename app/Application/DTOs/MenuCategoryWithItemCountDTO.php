<?php

namespace App\Application\DTOs;

use App\Domain\Entities\MenuCategory;

class MenuCategoryWithItemCountDTO
{
    public function __construct(
        public string $id,
        public string $menu_id,
        public array $name,
        public ?array $description,
        public int $sort_order,
        public int $items_count
    ) {
    }

    public static function fromMenuCategoryAndItemCount(MenuCategory $category, int $itemCount): self
    {
        return new self(
            id: $category->getId(),
            menu_id: $category->getMenuId(),
            name: $category->getName(),
            description: $category->getDescription(),
            sort_order: $category->getSortOrder(),
            items_count: $itemCount
        );
    }
} 