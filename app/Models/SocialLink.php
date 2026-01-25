<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    protected $fillable = [
        'platform',
        'type',
        'url',
        'is_active',
    ];

    public const TYPE_FOOTER = 'footer';
    public const TYPE_CABINET = 'cabinet';

    public function scopeFooter($query)
    {
        return $query->where('type', self::TYPE_FOOTER);
    }

    public function scopeCabinet($query)
    {
        return $query->where('type', self::TYPE_CABINET);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->whereNotNull('url');
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
