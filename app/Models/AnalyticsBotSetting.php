<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsBotSetting extends Model
{
    protected $fillable = [
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
     * Получить активные настройки бота для аналитики
     */
    public static function getActive(): ?self
    {
        return self::where('is_active', true)->first();
    }

    /**
     * Получить или создать единственный экземпляр настроек
     */
    public static function getInstance(): self
    {
        $settings = self::first();

        if (!$settings) {
            $settings = self::create([
                'bot_token' => null,
                'chat_id' => null,
                'is_active' => false,
            ]);
        }

        return $settings;
    }
}
