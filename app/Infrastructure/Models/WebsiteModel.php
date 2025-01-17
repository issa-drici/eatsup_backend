<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class WebsiteModel extends Model
{
    use HasUuids;

    protected $table = 'websites';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'restaurant_id',
        'domain',
        'title',
        'description',
        'presentation_image_id',
        'opening_hours',
        'theme_config'
    ];

    protected $casts = [
        'title' => 'json',
        'description' => 'json',
        'opening_hours' => 'json',
        'theme_config' => 'json'
    ];

    public function restaurant()
    {
        return $this->belongsTo(RestaurantModel::class, 'restaurant_id');
    }
} 