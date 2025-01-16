<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Restaurant;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Infrastructure\Models\RestaurantModel;
use App\Domain\Entities\User;
use App\Domain\Entities\File;
use App\Application\DTOs\RestaurantWithOwnerDTO;
use App\Application\DTOs\RestaurantWithOwnerAndQrCodeCountDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $restaurants = RestaurantModel::with(['owner', 'logo'])
            ->leftJoin('qr_codes', 'restaurants.id', '=', 'qr_codes.restaurant_id')
            ->join('users', 'restaurants.owner_id', '=', 'users.id')
            ->whereNull('qr_codes.id')
            ->select(
                'restaurants.*',
                'users.name as owner_name',
                'users.email as owner_email',
                'users.role as owner_role',
                'users.user_plan as owner_plan',
                'users.user_subscription_status as owner_subscription_status'
            )
            ->get();

        return $restaurants->map(function ($restaurant) {
            $file = null;
            if ($restaurant->logo) {
                $file = new File(
                    id: $restaurant->logo->id,
                    userId: $restaurant->logo->user_id,
                    path: $restaurant->logo->path,
                    url: $restaurant->logo->url,
                    filename: $restaurant->logo->filename,
                    mimeType: $restaurant->logo->mime_type,
                    size: $restaurant->logo->size,
                    createdAt: $restaurant->logo->created_at,
                    updatedAt: $restaurant->logo->updated_at
                );
            }

            return RestaurantWithOwnerDTO::fromRestaurantAndUser(
                $this->toDomainEntity($restaurant),
                $file,
                new User(
                    id: $restaurant->owner_id,
                    name: $restaurant->owner_name,
                    email: $restaurant->owner_email,
                    role: $restaurant->owner_role,
                    userPlan: $restaurant->owner_plan,
                    userSubscriptionStatus: $restaurant->owner_subscription_status
                )
            );
        })->all();
    }

    public function findByIdWithOwner(string $id): ?RestaurantWithOwnerDTO
    {
        $restaurant = RestaurantModel::with(['owner', 'logo'])
            ->where('restaurants.id', $id)
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

        $file = null;
        if ($restaurant->logo) {
            $file = new File(
                id: $restaurant->logo->id,
                userId: $restaurant->logo->user_id,
                path: $restaurant->logo->path,
                url: $restaurant->logo->url,
                filename: $restaurant->logo->filename,
                mimeType: $restaurant->logo->mime_type,
                size: $restaurant->logo->size,
                createdAt: $restaurant->logo->created_at,
                updatedAt: $restaurant->logo->updated_at
            );
        }

        return RestaurantWithOwnerDTO::fromRestaurantAndUser(
            $this->toDomainEntity($restaurant),
            $file,
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
        $restaurants = RestaurantModel::with('logo')
            ->join('users', 'restaurants.owner_id', '=', 'users.id')
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
            $file = null;
            if ($model->logo) {
                $file = new File(
                    id: $model->logo->id,
                    userId: $model->logo->user_id,
                    path: $model->logo->path,
                    url: $model->logo->url,
                    filename: $model->logo->filename,
                    mimeType: $model->logo->mime_type,
                    size: $model->logo->size,
                    createdAt: $model->logo->created_at,
                    updatedAt: $model->logo->updated_at
                );
            }

            return RestaurantWithOwnerDTO::fromRestaurantAndUser(
                $this->toDomainEntity($model),
                $file,
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
            ->with('logo')
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
                $file = null;
                if ($model->logo) {
                    $file = new File(
                        id: $model->logo->id,
                        userId: $model->logo->user_id,
                        path: $model->logo->path,
                        url: $model->logo->url,
                        filename: $model->logo->filename,
                        mimeType: $model->logo->mime_type,
                        size: $model->logo->size,
                        createdAt: $model->logo->created_at,
                        updatedAt: $model->logo->updated_at
                    );
                }

                return RestaurantWithOwnerDTO::fromRestaurantAndUser(
                    $this->toDomainEntity($model),
                    $file,
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

    public function findAllWithQrCodeCount(): array
    {
        $restaurants = RestaurantModel::join('users', 'restaurants.owner_id', '=', 'users.id')
            ->leftJoin('qr_codes', 'restaurants.id', '=', 'qr_codes.restaurant_id')
            ->select(
                'restaurants.*',
                'users.name as owner_name',
                'users.email as owner_email',
                'users.role as owner_role',
                'users.user_plan as owner_plan',
                'users.user_subscription_status as owner_subscription_status',
                DB::raw('COUNT(qr_codes.id) as qr_codes_count')
            )
            ->groupBy(
                'restaurants.id',
                'restaurants.owner_id',
                'restaurants.name',
                'restaurants.address',
                'restaurants.phone',
                'restaurants.created_at',
                'restaurants.updated_at',
                'users.name',
                'users.email',
                'users.role',
                'users.user_plan',
                'users.user_subscription_status'
            )
            ->get();

        return $restaurants->map(function ($model) {
            $restaurantDTO = RestaurantWithOwnerDTO::fromRestaurantAndUser(
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

            return RestaurantWithOwnerAndQrCodeCountDTO::fromRestaurantWithOwnerDTO(
                $restaurantDTO,
                $model->qr_codes_count
            );
        })->all();
    }

    public function update(Restaurant $restaurant): Restaurant
    {
        $model = RestaurantModel::findOrFail($restaurant->getId());
        
        $model->update([
            'name' => $restaurant->getName(),
            'address' => $restaurant->getAddress(),
            'phone' => $restaurant->getPhone(),
            'logo_id' => $restaurant->getLogoId(),
            'social_links' => $restaurant->getSocialLinks(),
            'google_info' => $restaurant->getGoogleInfo(),
        ]);

        return $this->toDomainEntity($model);
    }

    public function findById(string $id): ?Restaurant
    {
        $model = RestaurantModel::find($id);
        
        if (!$model) {
            return null;
        }
        
        return $this->toDomainEntity($model);
    }

    private function toDomainEntity(RestaurantModel $model): Restaurant
    {
        return new Restaurant(
            id: $model->id,
            ownerId: $model->owner_id,
            name: $model->name,
            address: $model->address,
            phone: $model->phone,
            logoId: $model->logo_id,
            socialLinks: $model->social_links,
            googleInfo: $model->google_info
        );
    }
}
