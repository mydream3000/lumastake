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
                'content' => 'This is a demo post to showcase the blog.',
                'seo_title' => 'Welcome to Lumastake',
                'seo_description' => 'Demo blog post',
                'published_at' => now(),
            ]
        );
    }
}
