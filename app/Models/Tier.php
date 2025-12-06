<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'level',
        'min_balance',
        'max_balance',
        'referral_percentage',
    ];

    public function percentages()
    {
        return $this->hasMany(TierPercentage::class);
    }

    public function islamicPercentages()
    {
        return $this->hasMany(TierPercentageIslamic::class);
    }

    public function investmentPools()
    {
        return $this->hasMany(InvestmentPool::class);
    }

    public function islamicInvestmentPools()
    {
        return $this->hasMany(InvestmentPoolIslamic::class);
    }
}
