@extends('layouts.public')

@section('content')
    {{-- HERO SECTION --}}
    <section class="relative pt-10 pb-20 overflow-hidden bg-white">
        <div class="max-w-[1440px] mx-auto px-4 md:px-12 relative z-10">
            <div class="bg-gradient-to-b from-[#3B4EFC] to-[#95D2FF] rounded-[34px] p-12 md:p-24 relative overflow-hidden min-h-[700px] flex flex-col items-center justify-center text-center">
                {{-- Background Chart Image --}}
                <div class="absolute inset-0 opacity-40 mix-blend-overlay pointer-events-none">
                    <img src="{{ asset('img/about/about-hero-bg.png') }}" alt="Chart" class="w-full h-full object-cover">
                </div>

                <div class="relative z-10">
                    <h3 class="text-3xl md:text-[52px] font-the-bold-font font-black text-white mb-4 uppercase tracking-tighter leading-[0.9]">
                        THE FUNDAMENTALS OF
                    </h3>
                    <h1 class="text-5xl md:text-7xl lg:text-[100px] font-the-bold-font font-black text-white leading-[0.9] mb-12 uppercase tracking-tighter">
                        {{ $post->title }}
                    </h1>

                    <div class="max-w-[1083px] mx-auto">
                        <p class="text-xl md:text-[28px] text-white leading-normal">
                            {!! $post->excerpt !!}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Main Image Overlay --}}
            <div class="mt-[-100px] md:mt-[-150px] relative z-20 max-w-[1185px] mx-auto">
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
        </div>
    </section>

    {{-- CONCLUSION AREA (From Figma) --}}
    <section class="py-12 bg-white relative">
        <div class="max-w-[1440px] mx-auto px-4 md:px-12">
            <div class="bg-white/47 border border-[#2BA6FF] rounded-[8px] p-12 relative shadow-[0_4px_4px_0_rgba(0,0,0,0.25)] flex flex-col justify-center min-h-[311px]">
                <div class="absolute left-0 top-[40px] bottom-[40px] w-[6px] bg-[#2BA6FF] rounded-[6px]"></div>
                <h2 class="text-4xl md:text-[40px] font-the-bold-font font-black text-[#3B4EFC] mb-8 uppercase leading-[0.9] ml-4 tracking-tighter">CONCLUSION</h2>
                <div class="text-2xl md:text-[28px] text-[#22253B]/79 leading-normal ml-4">
                    <p>Crypto arbitrage remains one of the most reliable strategies in a volatile market, offering the potential for steady, low-risk returns. With Luma Stake, the complexities of arbitrage are handled for you, allowing you to benefit from proven market strategies while focusing on your broader investment goals.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- RECENT NEWS SECTION (Dynamic) --}}
    <section class="py-20 bg-white">
        <div class="max-w-[1440px] mx-auto px-4 md:px-12">
            <h2 class="text-4xl md:text-[60px] font-the-bold-font font-black text-[#3B4EFC] mb-12 uppercase leading-[0.9] tracking-tighter">Latest News</h2>

            <div class="grid md:grid-cols-3 gap-8">
                @php
                    $latestPosts = \App\Models\BlogPost::active()->published()->where('id', '!=', $post->id)->orderByDesc('published_at')->take(3)->get();
                @endphp

                @foreach($latestPosts as $lPost)
                    <div class="bg-white border border-[#2BA6FF] rounded-[30px] p-6 shadow-[0_4px_4px_0_rgba(43,166,255,1)] flex flex-col transition-transform hover:scale-[1.02]">
                        <div class="h-[200px] mb-6 rounded-[20px] overflow-hidden">
                            <img src="{{ $lPost->image ? asset($lPost->image) : asset('img/blog/blog-article-main.png') }}" alt="{{ $lPost->title }}" class="w-full h-full object-cover">
                        </div>
                        <h3 class="text-2xl font-bold text-[#262262] mb-4 line-clamp-2 min-h-[64px]">{{ $lPost->title }}</h3>
                        <p class="text-gray-600 mb-6 line-clamp-3">{{ Str::limit(strip_tags($lPost->content), 120) }}</p>
                        <a href="{{ route('blog.show', $lPost->slug) }}" class="mt-auto text-[#3B4EFC] font-bold text-lg flex items-center gap-2 hover:translate-x-2 transition-transform uppercase">
                            Read More <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- FOOTER CTA AREA --}}
    <section class="py-20 bg-[#E0F2FF] relative overflow-hidden mt-12 rounded-[8px] mx-4 md:mx-12">
        <div class="absolute inset-0 opacity-40 mix-blend-overlay">
            <img src="{{ asset('img/about/about-hero-bg.png') }}" alt="Background" class="w-full h-full object-cover">
        </div>
        <div class="max-w-[1440px] mx-auto px-4 md:px-12 relative z-10 flex flex-col md:flex-row items-center justify-between">
            <div class="max-w-[727px]">
                <img src="{{ asset('img/about/logo-about.png') }}" alt="Luma Stake" class="h-[101px] mb-8">
                <p class="text-[32px] text-black leading-normal">
                    Your future shouldn’t depend on market luck. <br>
                    With <span class="font-bold">Lumastake</span>, you earn passively, <span class="font-bold">stake confidently</span>, and <span class="font-bold">sleep peacefully.</span>
                </p>
            </div>
            <div class="flex flex-col gap-4 mt-12 md:mt-0">
                <a href="{{ route('register') }}" class="bg-white/20 border border-[#2BA6FF] px-8 py-4 rounded-lg text-[28px] text-black/70 hover:bg-white/40 transition-all text-center">Get Started Now</a>
                <a href="{{ route('profit-tiers') }}" class="bg-white/20 border border-[#2BA6FF] px-8 py-4 rounded-lg text-[28px] text-black/70 hover:bg-white/40 transition-all text-center">Explore Plans</a>
                <a href="{{ route('contact') }}" class="bg-white/20 border border-[#2BA6FF] px-8 py-4 rounded-lg text-[28px] text-black/70 hover:bg-white/40 transition-all text-center">Contact Support</a>
            </div>
        </div>
    </section>
@endsection
