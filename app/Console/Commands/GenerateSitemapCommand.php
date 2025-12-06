<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\BlogPost;
use App\Models\Faq;
use Carbon\Carbon;

class GenerateSitemapCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate sitemap.xml for SEO';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating sitemap.xml...');

        $sitemap = Sitemap::create();

        // Add static pages
        $staticPages = [
            ['url' => route('home'), 'priority' => 1.0, 'changefreq' => 'daily'],
            ['url' => route('about'), 'priority' => 0.8, 'changefreq' => 'monthly'],
            ['url' => route('profit-tiers'), 'priority' => 0.9, 'changefreq' => 'weekly'],
            ['url' => route('blog'), 'priority' => 0.9, 'changefreq' => 'daily'],
            ['url' => route('faq'), 'priority' => 0.8, 'changefreq' => 'weekly'],
            ['url' => route('contact'), 'priority' => 0.7, 'changefreq' => 'monthly'],
            ['url' => route('terms'), 'priority' => 0.5, 'changefreq' => 'yearly'],
            ['url' => route('privacy'), 'priority' => 0.5, 'changefreq' => 'yearly'],
            ['url' => route('login'), 'priority' => 0.6, 'changefreq' => 'monthly'],
            ['url' => route('register'), 'priority' => 0.6, 'changefreq' => 'monthly'],
        ];

        foreach ($staticPages as $page) {
            $sitemap->add(
                Url::create($page['url'])
                    ->setLastModificationDate(Carbon::now())
                    ->setPriority($page['priority'])
                    ->setChangeFrequency($page['changefreq'])
            );
        }

        // Add blog posts (only active and published)
        BlogPost::query()
            ->active()
            ->published()
            ->orderByDesc('published_at')
            ->get()
            ->each(function (BlogPost $post) use ($sitemap) {
                $sitemap->add(
                    Url::create(route('blog.show', $post->slug))
                        ->setLastModificationDate($post->updated_at ?? $post->published_at ?? now())
                        ->setPriority(0.8)
                        ->setChangeFrequency('weekly')
                );
            });

        // Write to public directory
        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully at public/sitemap.xml');

        return Command::SUCCESS;
    }
}
