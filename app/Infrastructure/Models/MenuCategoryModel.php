<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MenuCategoryModel extends Model
{
    use HasUuids; // Si tu veux que Laravel gère l'UUID automatiquement
    // Sinon, tu peux gérer manuellement dans create()

    protected $table = 'menu_categories';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'menu_id',
        'name',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'name' => 'json',
        'description' => 'json',
        'sort_order' => 'integer',
    ];
}
