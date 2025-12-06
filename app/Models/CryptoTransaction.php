<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CryptoTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'network',
        'token',
        'address',
        'tx_hash',
        'amount',
        'confirmations',
        'processed',
        'ipn_data',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'confirmations' => 'integer',
        'processed' => 'boolean',
        'ipn_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
