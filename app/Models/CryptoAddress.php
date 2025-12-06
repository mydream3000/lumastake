<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CryptoAddress extends Model
{
    protected $fillable = [
        'user_id',
        'network',
        'token',
        'address',
        'public_key',
        'address_requested_at',
    ];

    protected $casts = [
        'address_requested_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
