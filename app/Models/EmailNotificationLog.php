<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailNotificationLog extends Model
{
    public $table = 'email_notifications_log';
    protected $fillable = [
        'user_id',
        'template_key',
        'email',
        'subject',
        'related_type',
        'related_id',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if notification was already sent
     */
    public static function wasAlreadySent(string $templateKey, string $relatedType, int $relatedId): bool
    {
        return static::where('template_key', $templateKey)
            ->where('related_type', $relatedType)
            ->where('related_id', $relatedId)
            ->exists();
    }

    /**
     * Log sent notification
     */
    public static function logSent(
        int $userId,
        string $templateKey,
        string $email,
        string $subject,
        ?string $relatedType = null,
        ?int $relatedId = null
    ): self {
        return static::create([
            'user_id' => $userId,
            'template_key' => $templateKey,
            'email' => $email,
            'subject' => $subject,
            'related_type' => $relatedType,
            'related_id' => $relatedId,
            'sent_at' => now(),
        ]);
    }
}
