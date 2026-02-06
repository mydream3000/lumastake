<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App\Models\BlogPost;

class BlogController extends BaseController
{
    public function index()
    {
        $posts = BlogPost::query()
            ->active()
            ->published()
            ->orderByDesc('published_at')
            ->paginate(6);

        $seoKey = 'blog';

        if (request()->ajax()) {
            return view('public.blog.partials.posts', compact('posts'))->render();
        }

        return view('public.blog.index', compact('posts', 'seoKey'));
    }

    public function show(string $slug)
    {
        $post = BlogPost::query()
            ->active()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment views
        $post->increment('views');

        // Fetch 3 random articles (excluding the current one)
        $randomPosts = BlogPost::query()
            ->active()
            ->published()
            ->where('id', '!=', $post->id)
            ->inRandomOrder()
            ->take(3)
            ->get();

        // Prepare SEO data from blog post fields
        $seo = [
            'title' => $post->seo_title ?: ($post->title . ' - Lumastake Blog'),
            'description' => $post->seo_description ?: $post->excerpt,
            'keywords' => 'blog, article, ' . $post->title,
            'og_title' => $post->seo_title ?: $post->title,
            'og_description' => $post->seo_description ?: $post->excerpt,
            'og_image' => $post->og_image ? asset($post->og_image) : ($post->image ? asset($post->image) : asset('images/og-image.jpg')),
            // Article-specific OG meta
            'article_published_time' => $post->published_at?->toIso8601String(),
            'article_modified_time' => $post->updated_at?->toIso8601String(),
            'article_author' => $post->author?->name,
            'og_section' => 'Blog',
            // Twitter
            'twitter_creator' => null,
        ];

        return view('public.blog.show', compact('post', 'seo', 'randomPosts'));
    }
}
