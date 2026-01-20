@extends('layouts.admin')

@section('title', 'SEO Management')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'home' }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">SEO Settings</h1>
            <p class="text-gray-600 mt-1">Manage SEO meta tags, robots.txt, and sitemap.xml</p>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="flex flex-wrap -mb-px">
                @foreach($pages as $key => $page)
                <button
                    type="button"
                    x-on:click="activeTab = '{{ $key }}'"
                    class="px-6 py-4 border-b-2 font-medium text-sm transition-colors"
                    x-bind:class="{ 'border-blue-500 text-blue-600': activeTab === '{{ $key }}', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== '{{ $key }}' }">
                    {{ $page['title'] }}
                </button>
                @endforeach
                <button
                    type="button"
                    x-on:click="activeTab = 'robots'"
                    class="px-6 py-4 border-b-2 font-medium text-sm transition-colors"
                    x-bind:class="{ 'border-blue-500 text-blue-600': activeTab === 'robots', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'robots' }">
                    Robots.txt
                </button>
                <button
                    type="button"
                    x-on:click="activeTab = 'sitemap'"
                    class="px-6 py-4 border-b-2 font-medium text-sm transition-colors"
                    x-bind:class="{ 'border-blue-500 text-blue-600': activeTab === 'sitemap', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'sitemap' }">
                    Sitemap
                </button>
            </nav>
        </div>

        <!-- Page SEO Forms -->
        @foreach($pages as $key => $page)
        <div x-show="activeTab === '{{ $key }}'" x-cloak class="p-6">
            <form action="{{ route('admin.seo.update-page', $key) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Page Title</label>
                    <input type="text" name="page_title" value="{{ $seoSettings[$key]->page_title ?? '' }}" maxlength="255" required
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    <p class="text-xs text-gray-500 mt-1">50-60 characters recommended</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                    <textarea name="meta_description" rows="3" maxlength="500" required
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">{{ $seoSettings[$key]->meta_description ?? '' }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">150-160 characters recommended</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Meta Keywords</label>
                    <input type="text" name="meta_keywords" value="{{ $seoSettings[$key]->meta_keywords ?? '' }}" maxlength="500"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">OG Title</label>
                        <input type="text" name="og_title" value="{{ $seoSettings[$key]->og_title ?? '' }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">OG Image URL</label>
                        <input type="url" name="og_image" value="{{ $seoSettings[$key]->og_image ?? '' }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">OG Description</label>
                    <textarea name="og_description" rows="2"
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">{{ $seoSettings[$key]->og_description ?? '' }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Twitter Site</label>
                    <input type="text" name="twitter_site" value="{{ $seoSettings[$key]->twitter_site ?? '' }}" placeholder="@@lumastake"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Schema.org JSON-LD (Optional)</label>
                    <textarea name="schema_json" rows="6"
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 font-mono text-sm">{{ $seoSettings[$key]->schema_json ?? '' }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Example: {&quot;@@context&quot;: &quot;https://schema.org&quot;, ...}</p>
                </div>
                <button type="submit" class="px-4 py-2 bg-cabinet-orange text-white rounded-lg hover:bg-cabinet-orange/90 transition-colors">
                    Update {{ $page['title'] }} SEO
                </button>
            </form>
        </div>
        @endforeach

        <!-- Robots.txt Tab -->
        <div x-show="activeTab === 'robots'" x-cloak class="p-6">
            <form action="{{ route('admin.seo.update-robots') }}" method="POST">
                @csrf
                <textarea name="robots_txt" rows="10" required
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 font-mono text-sm">{{ $globalSeo->robots_txt ?? '' }}</textarea>
                <button type="submit" class="mt-4 px-4 py-2 bg-cabinet-orange text-white rounded-lg hover:bg-cabinet-orange/90 transition-colors">
                    Update Robots.txt
                </button>
            </form>
        </div>

        <!-- Sitemap Tab -->
        <div x-show="activeTab === 'sitemap'" x-cloak class="p-6">
            @if($sitemapExists)
                <p class="text-sm text-gray-600 mb-4">Last generated: {{ $sitemapLastModified }}</p>
            @else
                <p class="text-sm text-gray-600 mb-4">Sitemap has not been generated yet.</p>
            @endif
            <form action="{{ route('admin.seo.generate-sitemap') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Generate Sitemap
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
