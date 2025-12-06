<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TierPercentage extends Model
{
    use HasFactory;

    protected $fillable = [
        'tier_id', 'days', 'percentage', 'min_stake', 'order',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'min_stake' => 'decimal:2',
    ];

    public function tier(): BelongsTo
    {
        return $this->belongsTo(Tier::class);
    }
}
