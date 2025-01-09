<?php

namespace App\Application\DTOs;

class RestaurantWithOwnerAndQrCodeCountDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $address,
        public ?string $phone,
        public array $owner,
        public int $qr_code_count
    ) {
    }

    public static function fromRestaurantWithOwnerDTO(RestaurantWithOwnerDTO $restaurantDTO, int $qrCodeCount): self
    {
        return new self(
            id: $restaurantDTO->id,
            name: $restaurantDTO->name,
            address: $restaurantDTO->address,
            phone: $restaurantDTO->phone,
            owner: $restaurantDTO->owner,
            qr_code_count: $qrCodeCount
        );
    }
} 