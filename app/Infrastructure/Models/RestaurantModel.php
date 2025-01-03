<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class RestaurantModel extends Model
{
    use HasUuids;

    protected $table = 'restaurants';
    protected $fillable = [
        'id',
        'owner_id',
        'name',
        'address',
        'phone',
        'logo_url',
        'social_links',
        'google_info',
    ];

    protected $casts = [
        'social_links' => 'json',
        'google_info' => 'json',
    ];
}
