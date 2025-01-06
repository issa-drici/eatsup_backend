<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MenuItemModel extends Model
{
    use HasUuids;

    protected $table = 'menu_items';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'category_id',
        'name',
        'description',
        'price',
        'allergens',
        'images',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'name' => 'json',
        'description' => 'json',
        'price' => 'float',
        'images' => 'json',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];
} 