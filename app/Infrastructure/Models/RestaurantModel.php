<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class RestaurantModel extends Model
{
    protected $table = 'restaurants';
    public $timestamps = true;
    protected $fillable = [
        'id',
        'owner_id',
        'name',
        'address',
        'phone',
        'logo_id',
        'social_links',
        'google_info'
    ];

    protected $casts = [
        'social_links' => 'array',
        'google_info' => 'array'
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function logo()
    {
        return $this->belongsTo(FileModel::class, 'logo_id');
    }
}
