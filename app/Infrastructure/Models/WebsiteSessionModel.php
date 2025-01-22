<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class WebsiteSessionModel extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'website_sessions';

    protected $fillable = [
        'website_id',
        'visited_at',
        'ip_address',
        'user_agent',
        'location'
    ];

    protected $casts = [
        'visited_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function website()
    {
        return $this->belongsTo(WebsiteModel::class, 'website_id');
    }
} 