@extends('layouts.public')

@section('content')
    {{-- HERO SECTION --}}
    <section class="relative pt-10 pb-20 overflow-hidden bg-white">
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
                    <h1 class="text-5xl md:text-7xl lg:text-[130px] font-the-bold-font font-black text-white leading-[0.9] mb-12 uppercase tracking-tighter">
                        LUMA BLOG
                    </h1>

                    <div class="max-w-[939px] mx-auto">
                        <p class="text-xl md:text-[28px] text-white leading-normal">
                            Explore the latest insights, strategies, and updates from the world of digital asset staking and crypto arbitrage.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ARTICLES GRID SECTION --}}
    <section class="py-20 bg-white relative overflow-hidden">
        {{-- Decorative background elements --}}
        <div class="absolute right-[-100px] top-[10%] w-[500px] h-[500px] bg-[#3B4EFC]/5 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute left-[-100px] bottom-[10%] w-[400px] h-[400px] bg-[#3B4EFC]/5 rounded-full blur-[80px] pointer-events-none"></div>

        <div class="max-w-[1440px] mx-auto px-4 md:px-12 relative z-10">
            @if($posts->count() > 0)
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($posts as $post)
                        <div class="bg-white border border-[#2BA6FF] rounded-[30px] p-8 shadow-[0_4px_4px_0_rgba(43,166,255,1)] flex flex-col transition-transform hover:scale-[1.02] min-h-[550px]">
                            <div class="h-[240px] mb-8 rounded-[24px] overflow-hidden">
                                <img src="{{ $post->image ? asset($post->image) : asset('img/blog/blog-article-main.png') }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                            </div>

                            <div class="flex items-center gap-4 mb-4">
                                <span class="px-4 py-1 bg-[#3B4EFC]/10 text-[#3B4EFC] rounded-full text-sm font-bold uppercase tracking-wider">Article</span>
                                <span class="text-gray-400 text-sm">{{ $post->published_at->format('M d, Y') }}</span>
                            </div>

                            <h3 class="text-[32px] font-the-bold-font font-black text-[#262262] mb-6 uppercase leading-[0.9] tracking-tighter line-clamp-2 min-h-[58px]">
                                {{ $post->title }}
                            </h3>

                            <p class="text-[#262262]/70 text-[18px] leading-relaxed mb-8 line-clamp-3">
                                {{ Str::limit(strip_tags($post->content), 150) }}
                            </p>

                            <a href="{{ route('blog.show', $post->slug) }}" class="mt-auto inline-flex items-center gap-2 text-[#3B4EFC] font-black text-xl uppercase tracking-tighter hover:gap-4 transition-all">
                                Read More <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="mt-16">
                    {{ $posts->links() }}
                </div>
            @else
                <div class="text-center py-20">
                    <h3 class="text-3xl font-the-bold-font text-gray-400 uppercase">No articles found yet.</h3>
                </div>
            @endif
        </div>
    </section>

    {{-- WHY DO PRICE DIFFERENCES OCCUR (FAQ Integration) --}}
    @if($faqs->count() > 0)
    <section class="py-24 bg-white overflow-hidden relative">
        <div class="max-w-[1440px] mx-auto px-4 md:px-12">
            <h2 class="text-5xl md:text-[80px] font-the-bold-font font-black text-[#3B4EFC] mb-16 uppercase leading-[0.9] tracking-tighter">
                WHY DO PRICE DIFFERENCES OCCUR ?
            </h2>

            <div class="grid md:grid-cols-2 gap-8 items-stretch">
                @foreach($faqs as $faq)
                    <div class="bg-white/47 backdrop-blur-sm border border-[#2BA6FF] p-10 rounded-[8px] min-h-[267px] flex flex-col justify-center shadow-[0_4px_4px_0_rgba(0,0,0,0.05)]">
                        <h3 class="text-4xl md:text-[40px] font-bold text-[#262262] mb-6 leading-[0.93]">{{ $faq->question }}</h3>
                        <p class="text-[22px] text-[#22253B]/79 leading-normal">
                            {{ $faq->answer }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

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
