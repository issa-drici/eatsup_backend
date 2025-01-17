<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Website;
use App\Domain\Repositories\WebsiteRepositoryInterface;
use App\Infrastructure\Models\WebsiteModel;

class EloquentWebsiteRepository implements WebsiteRepositoryInterface
{
    public function create(Website $website): Website
    {
        $model = WebsiteModel::create([
            'id' => $website->getId(),
            'restaurant_id' => $website->getRestaurantId(),
            'domain' => $website->getDomain(),
            'title' => $website->getTitle(),
            'description' => $website->getDescription(),
            'presentation_image_id' => $website->getPresentationImageId(),
            'opening_hours' => $website->getOpeningHours(),
            'theme_config' => $website->getThemeConfig()
        ]);

        return $this->toDomainEntity($model);
    }

    public function findById(string $id): ?Website
    {
        $model = WebsiteModel::find($id);
        return $model ? $this->toDomainEntity($model) : null;
    }

    public function findByRestaurantId(string $restaurantId): ?Website
    {
        $model = WebsiteModel::where('restaurant_id', $restaurantId)->first();
        return $model ? $this->toDomainEntity($model) : null;
    }

    public function update(Website $website): Website
    {
        $model = WebsiteModel::findOrFail($website->getId());
        
        $model->update([
            'domain' => $website->getDomain(),
            'title' => $website->getTitle(),
            'description' => $website->getDescription(),
            'presentation_image_id' => $website->getPresentationImageId(),
            'opening_hours' => $website->getOpeningHours(),
            'theme_config' => $website->getThemeConfig()
        ]);

        return $this->toDomainEntity($model->fresh());
    }

    public function delete(string $id): void
    {
        WebsiteModel::findOrFail($id)->delete();
    }

    private function toDomainEntity(WebsiteModel $model): Website
    {
        return new Website(
            id: $model->id,
            restaurantId: $model->restaurant_id,
            menuId: $model->menu_id,
            title: $model->title,
            description: $model->description,
            themeConfig: $model->theme_config,
            domain: $model->domain,
            presentationImageId: $model->presentation_image_id,
            openingHours: $model->opening_hours
        );
    }
} 