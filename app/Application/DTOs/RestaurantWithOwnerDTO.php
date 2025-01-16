<?php

namespace App\Application\DTOs;

class RestaurantWithOwnerDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $address,
        public ?string $phone,
        public ?array $logo,
        public ?array $social_links,
        public ?array $google_info,
        public array $owner
    ) {
    }

    public static function fromRestaurantAndUser($restaurant, $file, $user): self
    {
        return new self(
            id: $restaurant->getId(),
            name: $restaurant->getName(),
            address: $restaurant->getAddress(),
            phone: $restaurant->getPhone(),
            logo: $file ? [
                'id' => $file->getId(),
                'url' => $file->getUrl()
            ] : null,
            social_links: $restaurant->getSocialLinks(),
            google_info: $restaurant->getGoogleInfo(),
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