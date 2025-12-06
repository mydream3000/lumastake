<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BotSetting extends Model
{
    protected $fillable = [
        'bot_name',
        'bot_token',
        'chat_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Получить активные настройки бота
     */
    public static function getActive(): ?self
    {
        return self::where('is_active', true)->first();
    }
}
