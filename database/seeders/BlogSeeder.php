<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        BlogPost::query()->updateOrCreate(
            ['slug' => 'welcome-to-lumastake'],
            [
                'title' => 'Welcome to Lumastake',
                'content' => '<p>This is a demo post to showcase the blog. Lumastake is a cryptocurrency staking platform for USDT where users can deposit, create staking deposits with interest, withdraw funds, and earn referral rewards through a multi-tier system.</p><p>Crypto arbitrage is the practice of profiting from price discrepancies of the same digital asset across different exchanges or markets. Our platform uses advanced algorithms to identify and exploit these opportunities, providing consistent returns for our users.</p>',
                'seo_title' => 'Welcome to Lumastake',
                'seo_description' => 'Demo blog post about Lumastake cryptocurrency staking platform',
                'published_at' => now(),
                'is_active' => true,
                'color_scheme' => '#00ffa3',
                'author_id' => 1,
            ]
        );
    }
}
