<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'content', 'image', 'author_id', 'views', 'seo_title', 'seo_description', 'og_image', 'schema_json', 'published_at', 'is_active', 'color_scheme',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Scope для активных статей
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope для опубликованных статей
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    // Получить excerpt (первые ~500 символов для превью)
    public function getExcerptAttribute(): string
    {
        $text = strip_tags($this->content);
        if (mb_strlen($text) <= 500) {
            return $text;
        }
        return mb_substr($text, 0, 500) . '...';
    }
}
