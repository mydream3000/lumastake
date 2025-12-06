@extends('layouts.public')

@section('content')
<div class="bg-[#101221] min-h-screen relative py-16 md:py-24">
    <!-- Background decorative elements -->
    <div class="absolute right-0 top-32 w-96 h-96 opacity-20">
        <img src="{{ asset('assets/09af9496dcc2930ccbcbd9585a908dd018adf7b2.svg') }}" alt="" class="w-full h-full">
    </div>
    <div class="absolute left-0 bottom-32 w-80 h-80 opacity-15">
        <img src="{{ asset('assets/630ae6568f79e53581d752492d091b1828abdea6.svg') }}" alt="" class="w-full h-full">
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Back Button -->
        <div class="mb-8">
            <a href="{{ route('blog') }}"
               class="inline-flex items-center gap-2 text-white hover:text-[{{ $post->color_scheme }}] transition-colors">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Blog</span>
            </a>
        </div>

        <!-- Article Card -->
        <article class="bg-[rgba(34,37,59,0.5)] border border-[{{ $post->color_scheme }}] rounded-[31px] p-8 md:p-12 mb-16 relative">
            <!-- Mini color block -->
            <div class="absolute left-0 top-12 w-4 h-32 rounded-r" style="background-color: {{ $post->color_scheme }};"></div>

            <!-- Article Header -->
            <div class="mb-8">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-6 text-white">
                    {{ $post->title }}
                </h1>

                <div class="flex flex-wrap items-center gap-4 text-gray-400 text-sm">
                    <span class="flex items-center gap-2">
                        <i class="fas fa-calendar"></i>
                        {{ $post->published_at->format('d, M, Y') }}
                    </span>

                    @if($post->author)
                    <span class="flex items-center gap-2">
                        <i class="fas fa-user"></i>
                        {{ $post->author->name }}
                    </span>
                    @endif

{{--                    <span class="flex items-center gap-2">--}}
{{--                        <i class="fas fa-eye"></i>--}}
{{--                        {{ $post->views }} views--}}
{{--                    </span>--}}
                </div>
            </div>

            <!-- Featured Image -->
            @if($post->image)
            <div class="post-image mb-8">
                <img src="{{ asset($post->image) }}"
                     alt="{{ $post->title }}"
                     class="w-full h-auto rounded-2xl border-2"
                     style="border-color: {{ $post->color_scheme }};">
            </div>
            @endif

            <!-- Article Content -->
            <div class="prose prose-invert prose-lg max-w-none">
                <div class="text-white text-lg md:text-xl leading-relaxed whitespace-pre-wrap">{!! $post->content !!}</div>
            </div>

            <!-- Article Footer -->
            <div class="mt-12 pt-8 border-t border-gray-700">
                <div class="flex items-center justify-between">
{{--                    <div class="text-gray-400 text-sm">--}}
{{--                        Last updated: {{ $post->updated_at->format('d, M, Y') }}--}}
{{--                    </div>--}}

                    <!-- Share Buttons -->
                    <div class="flex items-center gap-3 flex-wrap">
                        <span class="text-gray-400 text-sm">Share:</span>
                        <a href="https://www.instagram.com/?url={{ urlencode(route('blog.show', $post->slug)) }}"
                           target="_blank"
                           class="w-10 h-10 rounded-full flex items-center justify-center transition-all hover:scale-110 border"
                           style="background-color: {{ $post->color_scheme }}; border-color: {{ $post->color_scheme }};">
                            <i class="fab fa-instagram text-white text-lg"></i>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.show', $post->slug)) }}"
                           target="_blank"
                           class="w-10 h-10 rounded-full flex items-center justify-center transition-all hover:scale-110 border"
                           style="background-color: {{ $post->color_scheme }}; border-color: {{ $post->color_scheme }};">
                            <i class="fab fa-facebook-f text-white text-lg"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('blog.show', $post->slug)) }}&text={{ urlencode($post->title) }}"
                           target="_blank"
                           class="w-10 h-10 rounded-full flex items-center justify-center transition-all hover:scale-110 border"
                           style="background-color: {{ $post->color_scheme }}; border-color: {{ $post->color_scheme }};">
                            <i class="fab fa-twitter text-white text-lg"></i>
                        </a>
                        <a href="https://www.tiktok.com/share?url={{ urlencode(route('blog.show', $post->slug)) }}"
                           target="_blank"
                           class="w-10 h-10 rounded-full flex items-center justify-center transition-all hover:scale-110 border"
                           style="background-color: {{ $post->color_scheme }}; border-color: {{ $post->color_scheme }};">
                            <i class="fab fa-tiktok text-white text-lg"></i>
                        </a>
                    </div>
                </div>
            </div>
        </article>

        <!-- Back to Blog Button -->
        <div class="text-center">
            <a href="{{ route('blog') }}"
               class="inline-flex items-center gap-2 px-8 py-4 rounded-lg font-semibold transition-colors"
               style="background-color: {{ $post->color_scheme }}; color: #101221;">
                <i class="fas fa-arrow-left"></i>
                Back to All Articles
            </a>
        </div>
    </div>
</div>
@endsection
