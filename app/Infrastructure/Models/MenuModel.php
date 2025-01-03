<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MenuModel extends Model
{
    use HasUuids;

    protected $table = 'menus';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'restaurant_id',
        'name',
        'status',
        'banner',
    ];

    protected $casts = [
        'name' => 'json',
    ];

    public function restaurant()
    {
        return $this->belongsTo(RestaurantModel::class, 'restaurant_id');
    }
}
