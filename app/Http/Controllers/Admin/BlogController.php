<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\ToastMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Display a listing of blog posts
     */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            $posts = BlogPost::with('author')->orderByDesc('created_at')->get();

            return response()->json([
                'data' => $posts->map(function ($post) {
                    return [
                        'id' => $post->id,
                        'title' => $post->title,
                        'author' => $post->author ? $post->author->name : 'Unknown',
                        'published_at' => $post->published_at ? $post->published_at->format('d, M, Y') : 'Not published',
                        'is_active' => $post->is_active ? 'Active' : 'Inactive',
                        'views' => $post->views,
                        'color_scheme' => $post->color_scheme,
                    ];
                }),
                'total' => $posts->count(),
                'per_page' => $posts->count(),
                'current_page' => 1,
                'last_page' => 1,
            ]);
        }

        return view('admin.blog.index');
    }

    /**
     * Show the form for creating a new blog post
     */
    public function create()
    {
        return view('admin.blog.create');
    }

    /**
     * Store a newly created blog post in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'schema_json' => 'nullable|string',
            'published_at' => 'nullable|date',
            'is_active' => 'nullable|boolean',
            'color_scheme' => 'nullable|string|in:#00ffa3,#ff451c,#ff00d8,#94c83e',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/blog'), $filename);
            $validated['image'] = 'images/blog/' . $filename;
        }

        // Handle OG image upload
        if ($request->hasFile('og_image')) {
            $ogImage = $request->file('og_image');
            $filename = 'og_' . time() . '_' . Str::slug(pathinfo($ogImage->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $ogImage->getClientOriginalExtension();
            $ogImage->move(public_path('images/blog'), $filename);
            $validated['og_image'] = 'images/blog/' . $filename;
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['author_id'] = auth()->id();

        BlogPost::create($validated);

        ToastMessage::create([
            'user_id' => auth()->id(),
            'message' => 'Blog post successfully created',
            'type' => 'success',
        ]);

        return redirect()->route('admin.blog.index');
    }

    /**
     * Show the form for editing the specified blog post
     */
    public function edit(BlogPost $blog)
    {
        return view('admin.blog.edit', compact('blog'));
    }

    /**
     * Update the specified blog post in database
     */
    public function update(Request $request, BlogPost $blog)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug,' . $blog->id,
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'schema_json' => 'nullable|string',
            'published_at' => 'nullable|date',
            'is_active' => 'nullable|boolean',
            'color_scheme' => 'nullable|string|in:#00ffa3,#ff451c,#ff00d8,#94c83e',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($blog->image && file_exists(public_path($blog->image))) {
                unlink(public_path($blog->image));
            }

            $image = $request->file('image');
            $filename = time() . '_' . Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/blog'), $filename);
            $validated['image'] = 'images/blog/' . $filename;
        }

        // Handle OG image upload
        if ($request->hasFile('og_image')) {
            // Delete old OG image if exists
            if ($blog->og_image && file_exists(public_path($blog->og_image))) {
                unlink(public_path($blog->og_image));
            }

            $ogImage = $request->file('og_image');
            $filename = 'og_' . time() . '_' . Str::slug(pathinfo($ogImage->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $ogImage->getClientOriginalExtension();
            $ogImage->move(public_path('images/blog'), $filename);
            $validated['og_image'] = 'images/blog/' . $filename;
        }

        $validated['is_active'] = $request->has('is_active');

        $blog->update($validated);

        ToastMessage::create([
            'user_id' => auth()->id(),
            'message' => 'Blog post successfully updated',
            'type' => 'success',
        ]);

        return redirect()->route('admin.blog.index');
    }

    /**
     * Remove the specified blog post from database
     */
    public function destroy(BlogPost $blog)
    {
        // Delete image if exists
        if ($blog->image && file_exists(public_path($blog->image))) {
            unlink(public_path($blog->image));
        }

        // Delete OG image if exists
        if ($blog->og_image && file_exists(public_path($blog->og_image))) {
            unlink(public_path($blog->og_image));
        }

        $blog->delete();

        ToastMessage::create([
            'user_id' => auth()->id(),
            'message' => 'Blog post successfully deleted',
            'type' => 'success',
        ]);

        return redirect()->route('admin.blog.index');
    }
}
