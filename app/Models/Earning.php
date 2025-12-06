<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Earning extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'description',
        'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope для фильтрации по типу earnings
     */
    public function scopeProfit($query)
    {
        return $query->where('type', 'profit');
    }

    public function scopeReferralReward($query)
    {
        return $query->where('type', 'referral_reward');
    }

    /**
     * Геттеры для мета-данных
     */
    public function getStakingDepositIdAttribute(): ?int
    {
        return $this->meta['staking_deposit_id'] ?? null;
    }

    public function getReferralIdAttribute(): ?int
    {
        return $this->meta['referral_id'] ?? null;
    }

    public function getReferralNameAttribute(): ?string
    {
        return $this->meta['referral_name'] ?? null;
    }

    public function getPercentageAttribute(): ?float
    {
        return isset($this->meta['percentage']) ? (float) $this->meta['percentage'] : null;
    }

    public function getRewardPercentageAttribute(): ?float
    {
        return isset($this->meta['reward_percentage']) ? (float) $this->meta['reward_percentage'] : null;
    }

    public function getTierNameAttribute(): ?string
    {
        return $this->meta['tier_name'] ?? null;
    }
}
