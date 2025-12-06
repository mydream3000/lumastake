<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StakingDeposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'tier_id', 'amount', 'days', 'percentage', 'earned_profit', 'start_date', 'end_date', 'status', 'auto_renewal',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'percentage' => 'decimal:2',
        'earned_profit' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'auto_renewal' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tier(): BelongsTo
    {
        return $this->belongsTo(Tier::class);
    }
}
