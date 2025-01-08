<?php

namespace App\Application\DTOs;

class RestaurantWithOwnerDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $address,
        public ?string $phone,
        public array $owner
    ) {
    }

    public static function fromRestaurantAndUser($restaurant, $user): self
    {
        return new self(
            id: $restaurant->getId(),
            name: $restaurant->getName(),
            address: $restaurant->getAddress(),
            phone: $restaurant->getPhone(),
            owner: [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'role' => $user->getRole(),
                'user_plan' => $user->getUserPlan(),
                'user_subscription_status' => $user->getUserSubscriptionStatus()
            ]
        );
    }
} 