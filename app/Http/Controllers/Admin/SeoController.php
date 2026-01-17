<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoSetting;
use App\Models\ToastMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class SeoController extends Controller
{
    /**
     * Show SEO settings page
     */
    public function index()
    {
        $seoTableMissing = !Schema::hasTable('seo_settings');

        $pages = [
            'home' => [
                'title' => 'Home Page',
                'defaults' => [
                    'page_title' => 'Lumastake - USDT Staking Platform',
                    'meta_description' => 'Earn passive income with USDT staking on Lumastake. Secure, reliable, and profitable cryptocurrency staking platform.',
                    'meta_keywords' => 'USDT, staking, cryptocurrency, passive income, Tether',
                ]
            ],
            'about' => [
                'title' => 'About Page',
                'defaults' => [
                    'page_title' => 'About Lumastake - Your Trusted USDT Staking Platform',
                    'meta_description' => 'Learn about Lumastake, our mission, and how we help you earn passive income through secure USDT staking.',
                    'meta_keywords' => 'about lumastake, company, mission, USDT staking',
                ]
            ],
            'tiers' => [
                'title' => 'Tiers Page',
                'defaults' => [
                    'page_title' => 'Staking Tiers - Lumastake',
                    'meta_description' => 'Explore our flexible staking tiers and choose the best option for your investment goals.',
                    'meta_keywords' => 'staking tiers, investment plans, APR rates',
                ]
            ],
            'blog' => [
                'title' => 'Blog Page',
                'defaults' => [
                    'page_title' => 'Blog - Latest News and Updates | Lumastake',
                    'meta_description' => 'Stay updated with the latest cryptocurrency news, staking tips, and platform updates from Lumastake.',
                    'meta_keywords' => 'blog, news, updates, cryptocurrency, staking tips',
                ]
            ],
            'contact' => [
                'title' => 'Contact Page',
                'defaults' => [
                    'page_title' => 'Contact Us - Lumastake Support',
                    'meta_description' => 'Get in touch with Lumastake support team. We are here to help you with any questions about USDT staking.',
                    'meta_keywords' => 'contact, support, help, customer service',
                ]
            ],
            'faq' => [
                'title' => 'FAQ Page',
                'defaults' => [
                    'page_title' => 'FAQ - Frequently Asked Questions | Lumastake',
                    'meta_description' => 'Find answers to common questions about USDT staking, deposits, withdrawals, and more.',
                    'meta_keywords' => 'FAQ, questions, answers, help, support',
                ]
            ],
        ];

        $seoSettings = [];

        if ($seoTableMissing) {
            foreach ($pages as $key => $page) {
                $seoSettings[$key] = new SeoSetting(array_merge(['key' => $key], $page['defaults']));
            }

            $globalSeo = new SeoSetting([
                'key' => 'global',
                'robots_txt' => "User-agent: *\nAllow: /\n\nSitemap: " . url('/sitemap.xml'),
            ]);
        } else {
            foreach ($pages as $key => $page) {
                $seoSettings[$key] = SeoSetting::firstOrCreate(
                    ['key' => $key],
                    $page['defaults']
                );
            }

            $globalSeo = SeoSetting::firstOrCreate(
                ['key' => 'global'],
                ['robots_txt' => "User-agent: *\nAllow: /\n\nSitemap: " . url('/sitemap.xml')]
            );
        }

        // Check if sitemap.xml exists
        $sitemapExists = file_exists(public_path('sitemap.xml'));
        $sitemapLastModified = $sitemapExists ? date('Y-m-d H:i:s', filemtime(public_path('sitemap.xml'))) : null;

        return view('admin.seo.index', compact('seoSettings', 'pages', 'globalSeo', 'sitemapExists', 'sitemapLastModified', 'seoTableMissing'));
    }

    /**
     * Update page SEO settings
     */
    public function updatePage(Request $request, string $pageKey)
    {
        $validPages = ['home', 'about', 'tiers', 'blog', 'contact', 'faq'];

        if (!in_array($pageKey, $validPages)) {
            abort(404);
        }

        $request->validate([
            'page_title' => 'required|string|max:255',
            'meta_description' => 'required|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|url|max:500',
            'twitter_site' => 'nullable|string|max:255',
            'schema_json' => 'nullable|string',
        ]);

        $pageSeo = SeoSetting::firstOrCreate(['key' => $pageKey]);
        $pageSeo->update($request->only([
            'page_title',
            'meta_description',
            'meta_keywords',
            'og_title',
            'og_description',
            'og_image',
            'twitter_site',
            'schema_json',
        ]));

        ToastMessage::create([
            'user_id' => auth()->id(),
            'message' => ucfirst($pageKey) . ' page SEO settings updated successfully',
            'type' => 'success',
        ]);

        return redirect()->route('admin.seo.index');
    }

    /**
     * Update robots.txt
     */
    public function updateRobots(Request $request)
    {
        $request->validate([
            'robots_txt' => 'required|string',
        ]);

        $globalSeo = SeoSetting::firstOrCreate(['key' => 'global']);
        $globalSeo->update(['robots_txt' => $request->robots_txt]);

        ToastMessage::create([
            'user_id' => auth()->id(),
            'message' => 'Robots.txt updated successfully',
            'type' => 'success',
        ]);

        return redirect()->route('admin.seo.index');
    }

    /**
     * Generate sitemap.xml
     */
    public function generateSitemap()
    {
        try {
            Artisan::call('sitemap:generate');

            ToastMessage::create([
                'user_id' => auth()->id(),
                'message' => 'Sitemap.xml generated successfully',
                'type' => 'success',
            ]);
        } catch (\Exception $e) {
            ToastMessage::create([
                'user_id' => auth()->id(),
                'message' => 'Failed to generate sitemap: ' . $e->getMessage(),
                'type' => 'error',
            ]);
        }

        return redirect()->route('admin.seo.index');
    }
}
