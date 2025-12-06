<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailSetting extends Model
{
    protected $fillable = [
        'sender_email',
        'sender_name',
        'support_email',
        'footer_text',
        'footer_support',
    ];

    protected $casts = [
        'footer_support' => 'boolean',
    ];

    /**
     * Get the singleton instance of email settings
     */
    public static function getInstance(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'sender_email' => 'no-reply@lumastake.com',
                'sender_name' => 'Lumastake',
                'support_email' => 'support@lumastake.com',
                'footer_text' => '© ' . date('Y') . ' Lumastake. All rights reserved.',
                'footer_support' => true,
            ]
        );
    }
}
