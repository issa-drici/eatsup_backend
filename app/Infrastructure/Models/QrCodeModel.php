<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrCodeModel extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $table = 'qr_codes';
    protected $fillable = [
        'id',
        'restaurant_id',
        'menu_id',
        'qr_type',
        'label',
        'status'
    ];

    public function restaurant()
    {
        return $this->belongsTo(RestaurantModel::class);
    }

    public function menu()
    {
        return $this->belongsTo(MenuModel::class);
    }
}
