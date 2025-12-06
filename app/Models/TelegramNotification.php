<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramNotification extends Model
{
    protected $fillable = [
        'transaction_id',
        'crypto_transaction_id',
        'message_type',
        'message_text',
        'tx_hash',
        'sent_at',
        'response',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'response' => 'array',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function cryptoTransaction(): BelongsTo
    {
        return $this->belongsTo(CryptoTransaction::class);
    }
}
