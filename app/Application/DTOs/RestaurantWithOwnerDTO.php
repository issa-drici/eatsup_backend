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
        public ?string $postal_code,
        public ?string $city,
        public ?string $city_slug,
        public ?string $type_slug,
        public ?string $name_slug,
        public array $owner
    ) {
    }

    public static function fromRestaurantAndUser($restaurant, $file, $user): self
    {
        return new self(
            id: $restaurant->getId(),
            name: $restaurant->getName(),
            address: $restaurant->getAddress(),
            postal_code: $restaurant->getPostalCode(),
            city: $restaurant->getCity(),
            city_slug: $restaurant->getCitySlug(),
            type_slug: $restaurant->getTypeSlug(),
            name_slug: $restaurant->getNameSlug(),
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