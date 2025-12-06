<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'start_balance', 'is_active', 'max_uses', 'uses_per_user', 'used_count',
    ];

    protected $casts = [
        'start_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
