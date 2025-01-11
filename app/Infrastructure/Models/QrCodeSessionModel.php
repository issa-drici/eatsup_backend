<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class QrCodeSessionModel extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'qr_code_sessions';

    protected $fillable = [
        'qr_code_id',
        'scanned_at',
        'ip_address',
        'user_agent',
        'location'
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function qrCode()
    {
        return $this->belongsTo(QrCodeModel::class, 'qr_code_id');
    }
} 