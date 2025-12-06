<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentPool extends Model
{
    protected $fillable = [
        'tier_id',
        'name',
        'days',
        'min_stake',
        'percentage',
        'is_active',
        'order',
    ];

    protected $casts = [
        'min_stake' => 'decimal:2',
        'percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function tier()
    {
        return $this->belongsTo(\App\Models\Tier::class);
    }
}
