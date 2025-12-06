<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentPoolIslamic extends Model
{
    use HasFactory;

    protected $table = 'investment_pools_islamic';

    protected $fillable = [
        'tier_id',
        'duration_days',
        'min_percentage',
        'max_percentage',
    ];

    protected function casts(): array
    {
        return [
            'duration_days' => 'integer',
            'min_percentage' => 'float',
            'max_percentage' => 'float',
        ];
    }

    public function tier()
    {
        return $this->belongsTo(Tier::class);
    }
}
