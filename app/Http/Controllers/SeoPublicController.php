<?php

namespace App\Http\Controllers;

use App\Models\SeoSetting;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;

class SeoPublicController extends Controller
{
    /**
     * Serve robots.txt from database or default
     */
    public function robots(): Response
    {
        // Try to get robots.txt from database
        $content = SeoSetting::getRobotsTxt();

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    /**
     * Serve sitemap.xml - auto-generate if it doesn't exist
     */
    public function sitemap(): Response
    {
        $sitemapPath = public_path('sitemap.xml');

        if (!file_exists($sitemapPath)) {
            // Generate sitemap if it doesn't exist
            try {
                Artisan::call('sitemap:generate');
            } catch (\Exception $e) {
                \Log::error('Failed to generate sitemap: ' . $e->getMessage());
                // Return empty sitemap as fallback
                $content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"></urlset>\n";
                return response($content, 200, [
                    'Content-Type' => 'application/xml; charset=UTF-8',
                ]);
            }
        }

        $content = file_get_contents($sitemapPath);

        return response($content, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
