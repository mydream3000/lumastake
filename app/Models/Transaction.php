<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'type', 'status', 'amount', 'is_real', 'description', 'tx_hash', 'meta', 'wallet_address', 'network', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_real' => 'boolean',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
