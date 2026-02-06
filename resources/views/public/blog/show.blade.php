@extends('layouts.public')

@section('content')
    {{-- HERO SECTION --}}
    <section class="relative pt-32 pb-20 overflow-hidden bg-white">
        <div class="max-w-[1440px] mx-auto px-4 md:px-12 relative z-10">
            <div class="bg-gradient-to-b from-[#3B4EFC] to-[#95D2FF] rounded-[34px] p-12 md:p-24 relative overflow-hidden min-h-[500px] flex flex-col items-center justify-center text-center">
                {{-- Background Chart Image --}}
                <div class="absolute inset-0 opacity-40 mix-blend-overlay pointer-events-none">
                    <img src="{{ asset('img/about/about-hero-bg.png') }}" alt="Chart" class="w-full h-full object-cover">
                </div>

                <div class="relative z-10">
                    <h3 class="text-3xl md:text-[52px] font-the-bold-font font-black text-white mb-4 uppercase tracking-tighter leading-[0.9]">
                        STAY UPDATED WITH
                    </h3>
                    <h1 class="text-5xl md:text-7xl lg:text-[100px] font-the-bold-font font-black text-white leading-[0.9] mb-12 uppercase tracking-tighter">
                        {{ $post->title }}
                    </h1>
                </div>
            </div>

            {{-- Main Image Overlay --}}
            <div class="mt-[-100px] md:mt-[-150px] relative z-20 max-w-[1185px] mx-auto px-4">
                <div class="bg-white border border-[#2BA6FF] rounded-[30px] p-2 shadow-[0_4px_4px_0_rgba(43,166,255,1)] overflow-hidden">
                    <img src="{{ $post->image ? asset($post->image) : asset('img/blog/blog-article-main.png') }}" alt="{{ $post->title }}" class="w-full h-auto rounded-[30px] object-cover max-h-[563px]">
                </div>
            </div>
        </div>
    </section>

    {{-- ARTICLE CONTENT SECTION --}}
    <section class="py-20 bg-white relative overflow-hidden">
        {{-- Decorative Ellipses --}}
        <div class="absolute right-[-100px] top-[10%] w-[500px] h-[500px] bg-[#3B4EFC]/5 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute left-[-100px] bottom-[10%] w-[400px] h-[400px] bg-[#3B4EFC]/5 rounded-full blur-[80px] pointer-events-none"></div>

        <div class="max-w-[1440px] mx-auto px-4 md:px-12 relative z-10">
            <div class="prose prose-2xl max-w-none prose-headings:font-the-bold-font prose-headings:text-[#3B4EFC] prose-headings:uppercase prose-headings:tracking-tighter prose-p:text-[22px] prose-p:text-black/70 prose-p:leading-normal">
                {!! $post->content !!}
            </div>

            {{-- Share Article Section --}}
            @if(isset($footerLinks) && $footerLinks->count() > 0)
                <div class="mt-16 pt-8 border-t border-gray-200">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                        <h3 class="text-2xl font-the-bold-font font-black text-[#262262] uppercase tracking-tighter">Share this article</h3>
                        <div class="flex items-center gap-4">
                            @php
                                $shareUrl = urlencode(request()->url());
                                $shareTitle = urlencode($post->title);
                            @endphp
                            @foreach($footerLinks as $link)
                                @php
                                    $shareLink = match($link->platform) {
                                        'Facebook' => "https://www.facebook.com/sharer/sharer.php?u={$shareUrl}",
                                        'Twitter' => "https://twitter.com/intent/tweet?url={$shareUrl}&text={$shareTitle}",
                                        'Telegram' => "https://t.me/share/url?url={$shareUrl}&text={$shareTitle}",
                                        default => null
                                    };
                                    $icon = match($link->platform) {
                                        'Facebook' => 'img/facebook.svg',
                                        'Twitter' => 'img/xlink.svg',
                                        'Telegram' => 'img/telegram.svg',
                                        default => null
                                    };
                                @endphp
                                @if($shareLink && $icon)
                                    <a href="{{ $shareLink }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="w-12 h-12 bg-[#3B4EFC] rounded-full flex items-center justify-center hover:bg-[#3B4EFC]/80 transition shadow-lg">
                                        <img src="{{ asset($icon) }}" alt="Share on {{ $link->platform }}" class="w-6 h-6 brightness-0 invert">
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- RECENT NEWS SECTION (Dynamic) --}}
    <section class="py-20 bg-white">
        <div class="max-w-[1440px] mx-auto px-4 md:px-12">
            <h2 class="text-4xl md:text-[60px] font-the-bold-font font-black text-[#3B4EFC] mb-12 uppercase leading-[0.9] tracking-tighter">Recommended Articles</h2>

            <div class="grid md:grid-cols-3 gap-8">
                @if($randomPosts->count() > 0)
                    @foreach($randomPosts as $lPost)
                        <div class="bg-white border border-[#2BA6FF] rounded-[30px] p-8 shadow-[0_4px_4px_0_rgba(43,166,255,1)] flex flex-col transition-transform hover:scale-[1.02] min-h-[550px]">
                            <div class="h-[240px] mb-8 rounded-[24px] overflow-hidden">
                                <img src="{{ $lPost->image ? asset($lPost->image) : asset('img/blog/blog-article-main.png') }}" alt="{{ $lPost->title }}" class="w-full h-full object-cover">
                            </div>

                            <div class="flex items-center gap-4 mb-4">
                                <span class="px-4 py-1 bg-[#3B4EFC]/10 text-[#3B4EFC] rounded-full text-sm font-bold uppercase tracking-wider">Article</span>
                                <span class="text-gray-400 text-sm">{{ $lPost->published_at ? $lPost->published_at->format('M d, Y') : $lPost->created_at->format('M d, Y') }}</span>
                            </div>

                            <h3 class="text-[32px] font-the-bold-font font-black text-[#262262] mb-6 uppercase leading-[0.9] tracking-tighter line-clamp-2 min-h-[58px]">
                                {{ $lPost->title }}
                            </h3>

                            <p class="text-[#262262]/70 text-[18px] leading-relaxed mb-8 line-clamp-3">
                                {{ Str::limit(strip_tags($lPost->content), 150) }}
                            </p>

                            <a href="{{ route('blog.show', $lPost->slug) }}" target="_blank" class="mt-auto inline-flex items-center gap-2 text-[#3B4EFC] font-black text-xl uppercase tracking-tighter hover:gap-4 transition-all">
                                Read More <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-3 text-center py-10">
                        <p class="text-gray-500 text-xl">No other articles found.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
