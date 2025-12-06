<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoSetting extends Model
{
    protected $fillable = [
        'key',
        'page_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'og_type',
        'twitter_card',
        'twitter_site',
        'twitter_creator',
        'schema_json',
        'robots_txt',
        'index',
        'follow',
    ];

    protected $casts = [
        'index' => 'boolean',
        'follow' => 'boolean',
    ];

    /**
     * Get SEO settings by key
     */
    public static function getByKey(string $key): ?self
    {
        return static::where('key', $key)->first();
    }

    /**
     * Get robots.txt content
     */
    public static function getRobotsTxt(): string
    {
        $global = static::getByKey('global');

        if ($global && $global->robots_txt) {
            return $global->robots_txt;
        }

        // Default robots.txt
        return "User-agent: *\nAllow: /\n\nSitemap: " . url('/sitemap.xml');
    }

    /**
     * Get meta robots directive
     */
    public function getMetaRobots(): string
    {
        $directives = [];

        if ($this->index) {
            $directives[] = 'index';
        } else {
            $directives[] = 'noindex';
        }

        if ($this->follow) {
            $directives[] = 'follow';
        } else {
            $directives[] = 'nofollow';
        }

        return implode(', ', $directives);
    }
}
