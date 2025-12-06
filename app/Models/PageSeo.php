<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSeo extends Model
{
    protected $table = 'page_seo';

    protected $fillable = [
        'page_slug',
        'page_name',
        'seo_title',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * Получить SEO данные для конкретной страницы
     */
    public static function getForPage(string $slug): ?self
    {
        return self::where('page_slug', $slug)->first();
    }
}
