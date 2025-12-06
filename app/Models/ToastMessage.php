<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToastMessage extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'message',
        'redirect_url',
        'reload',
        'auto_refresh',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'reload' => 'boolean',
        'auto_refresh' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function createForUser(int $userId, string $type, string $message, ?string $redirectUrl = null, bool $reload = false, bool $autoRefresh = false): self
    {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'message' => $message,
            'redirect_url' => $redirectUrl,
            'reload' => $reload,
            'auto_refresh' => $autoRefresh,
        ]);
    }

    public static function createForAdmin(string $type, string $message, bool $reload = false): void
    {
        // Получаем всех админов
        $admins = \App\Models\User::where('is_admin', true)->get();

        foreach ($admins as $admin) {
            self::create([
                'user_id' => $admin->id,
                'type' => $type,
                'message' => $message,
                'reload' => $reload,
            ]);
        }
    }

    public function markAsRead(): void
    {
        $this->update(['is_read' => true]);
    }
}
