<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendingWithdrawal extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'amount',
        'receiver_address',
        'network',
        'verification_code',
        'code_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'code_expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Проверить, истёк ли код
     */
    public function isExpired(): bool
    {
        return $this->code_expires_at < now();
    }
}
