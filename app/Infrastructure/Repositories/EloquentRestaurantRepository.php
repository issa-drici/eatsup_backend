<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Restaurant;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Infrastructure\Models\RestaurantModel;
use App\Domain\Entities\User;
use App\Application\DTOs\RestaurantWithOwnerDTO;

class EloquentRestaurantRepository implements RestaurantRepositoryInterface
{
    public function create(Restaurant $restaurant): Restaurant
    {
        $model = RestaurantModel::create([
            'id'          => $restaurant->getId(),
            'owner_id'    => $restaurant->getOwnerId(),
            'name'        => $restaurant->getName(),
            'address'     => $restaurant->getAddress(),
            'phone'       => $restaurant->getPhone(),
        ]);
        return $this->toDomainEntity($model);
    }

    public function findByOwnerId(string $ownerId): ?Restaurant
    {
        $model = RestaurantModel::where('owner_id', $ownerId)->first();
        if (!$model) {
            return null;
        }

        return new Restaurant(
            id: $model->id,
            ownerId: $model->owner_id,
            name: $model->name,
            address: $model->address,
            phone: $model->phone
        );
    }

    public function findAllWithoutQRCode(): array
    {
        $restaurants = RestaurantModel::whereNotExists(function ($query) {
                $query->select('id')
                      ->from('qr_codes')
                      ->whereRaw('restaurants.id = qr_codes.restaurant_id');
            })
            ->join('users', 'restaurants.owner_id', '=', 'users.id')
            ->select('restaurants.*', 'users.name as owner_name', 'users.email as owner_email', 
                    'users.role as owner_role', 'users.user_plan as owner_plan', 
                    'users.user_subscription_status as owner_subscription_status')
            ->get();

        return $restaurants->map(function ($model) {
            return RestaurantWithOwnerDTO::fromRestaurantAndUser(
                $this->toDomainEntity($model),
                new User(
                    id: $model->owner_id,
                    name: $model->owner_name,
                    email: $model->owner_email,
                    role: $model->owner_role,
                    userPlan: $model->owner_plan,
                    userSubscriptionStatus: $model->owner_subscription_status
                )
            );
        })->all();
    }

    public function findByIdWithOwner(string $id): ?RestaurantWithOwnerDTO
    {
        $restaurant = RestaurantModel::where('restaurants.id', $id)
            ->join('users', 'restaurants.owner_id', '=', 'users.id')
            ->select(
                'restaurants.*',
                'users.name as owner_name',
                'users.email as owner_email',
                'users.role as owner_role',
                'users.user_plan as owner_plan',
                'users.user_subscription_status as owner_subscription_status'
            )
            ->first();

        if (!$restaurant) {
            return null;
        }

        return RestaurantWithOwnerDTO::fromRestaurantAndUser(
            $this->toDomainEntity($restaurant),
            new User(
                id: $restaurant->owner_id,
                name: $restaurant->owner_name,
                email: $restaurant->owner_email,
                role: $restaurant->owner_role,
                userPlan: $restaurant->owner_plan,
                userSubscriptionStatus: $restaurant->owner_subscription_status
            )
        );
    }

    public function findAllWithOwners(): array
    {
        $restaurants = RestaurantModel::join('users', 'restaurants.owner_id', '=', 'users.id')
            ->select(
                'restaurants.*',
                'users.name as owner_name',
                'users.email as owner_email',
                'users.role as owner_role',
                'users.user_plan as owner_plan',
                'users.user_subscription_status as owner_subscription_status'
            )
            ->get();

        return $restaurants->map(function ($model) {
            return RestaurantWithOwnerDTO::fromRestaurantAndUser(
                $this->toDomainEntity($model),
                new User(
                    id: $model->owner_id,
                    name: $model->owner_name,
                    email: $model->owner_email,
                    role: $model->owner_role,
                    userPlan: $model->owner_plan,
                    userSubscriptionStatus: $model->owner_subscription_status
                )
            );
        })->all();
    }

    public function findAllWithOwnersPaginated(array $filters = [], int $page = 1, int $perPage = 10): array
    {
        $query = RestaurantModel::query()
            ->join('users', 'restaurants.owner_id', '=', 'users.id')
            ->select(
                'restaurants.*',
                'users.name as owner_name',
                'users.email as owner_email',
                'users.role as owner_role',
                'users.user_plan as owner_plan',
                'users.user_subscription_status as owner_subscription_status'
            );

        // Appliquer les filtres
        foreach ($filters as $key => $value) {
            switch ($key) {
                case 'name':
                    $query->whereRaw('LOWER(restaurants.name) LIKE ?', ['%' . strtolower($value) . '%']);
                    break;
                case 'address':
                    $query->where('restaurants.address', 'like', "%{$value}%");
                    break;
                case 'phone':
                    $query->where('restaurants.phone', 'like', "%{$value}%");
                    break;
                case 'owner_name':
                    $query->where('users.name', 'like', "%{$value}%");
                    break;
                case 'owner_email':
                    $query->where('users.email', 'like', "%{$value}%");
                    break;
                case 'owner_plan':
                    $query->where('users.user_plan', $value);
                    break;
            }
        }

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);
        $restaurants = $paginator->items();

        return [
            'data' => array_map(function ($model) {
                return RestaurantWithOwnerDTO::fromRestaurantAndUser(
                    $this->toDomainEntity($model),
                    new User(
                        id: $model->owner_id,
                        name: $model->owner_name,
                        email: $model->owner_email,
                        role: $model->owner_role,
                        userPlan: $model->owner_plan,
                        userSubscriptionStatus: $model->owner_subscription_status
                    )
                );
            }, $restaurants),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage()
        ];
    }

    private function toDomainEntity(RestaurantModel $model): Restaurant
    {
        return new Restaurant(
            id: $model->id,
            ownerId: $model->owner_id,
            name: $model->name,
            address: $model->address,
            phone: $model->phone
        );
    }
}
