<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReferralLevel extends Model
{
    protected $fillable = [
        'level',
        'name',
        'min_partners',
        'reward_percentage',
    ];

    protected $casts = [
        'level' => 'integer',
        'min_partners' => 'integer',
        'reward_percentage' => 'decimal:2',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
