<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'key',
        'name',
        'subject',
        'sender_name',
        'content',
        'variables',
        'enabled',
    ];

    protected $casts = [
        'variables' => 'array',
        'enabled' => 'boolean',
    ];

    /**
     * Get template by key
     */
    public static function getByKey(string $key): ?self
    {
        return static::where('key', $key)->where('enabled', true)->first();
    }

    /**
     * Render the template content with variables
     */
    public function render(array $data = []): string
    {
        // Create a temporary Blade view from the content
        $blade = \Illuminate\Support\Facades\Blade::compileString($this->content);

        // Render the compiled Blade with data
        return eval('?>' . $blade);
    }
}
