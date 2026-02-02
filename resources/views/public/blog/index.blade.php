@extends('layouts.public')

@section('content')
    {{-- HERO SECTION --}}
    <section class="relative pt-32 pb-[250px] md:pb-[280px] lg:pb-[360px] overflow-hidden bg-white">
        <div class="max-w-[1440px] mx-auto px-4 md:px-12 relative">
            <div
                class="relative overflow-visible rounded-[34px]
             bg-gradient-to-b from-[#3B4EFC] to-[#95D2FF]
             pt-12 md:pt-24
             px-6 md:px-16
             pb-[300px] md:pb-[360px] lg:pb-[360px]
             min-h-[640px] md:min-h-[760px]
             flex flex-col items-center text-center"
            >
                <!-- Background chart -->
                <div class="absolute inset-0 opacity-40 mix-blend-overlay pointer-events-none rounded-[34px] overflow-hidden">
                    <img src="{{ asset('img/about/about-hero-bg.png') }}" alt="Chart"
                         class="w-full h-full object-cover" />
                </div>

                <!-- Text -->
                <div class="relative z-10 max-w-[1000px] mx-auto">
                    <h3 class="text-3xl md:text-[52px] font-the-bold-font font-black text-white mb-4 uppercase tracking-tighter leading-[0.9]">
                        THE FUNDAMENTALS OF
                    </h3>

                    <h1 class="text-5xl md:text-7xl lg:text-[100px] font-bold text-white leading-[0.9] mb-8 md:mb-10 uppercase tracking-tighter">
                        CRYPTO ARBITRAGE
                    </h1>

                    <p class="text-lg md:text-[28px] text-white/95 leading-normal max-w-[939px] mx-auto">
                        The cryptocurrency market operates around the clock, with prices fluctuating by the second. This constant movement creates opportunities for those who know where to look. Among the many strategies available, <span class="font-bold text-shadow-yellow-300"> crypto arbitrage </span> stands out as a disciplined, market-neutral approach designed to capture consistent returns from temporary price inefficiencies.
                    </p>
                </div>

                <!-- Bottom image (overlapping) -->
                <div
                    class="absolute left-1/2 -translate-x-1/2
               bottom-[-170px] md:bottom-[-190px] lg:bottom-[-280px]
               z-20
               w-[92%] md:w-[86%] lg:w-[78%]
               max-w-[1200px]"
                >
                    <div class="rounded-[28px] overflow-hidden shadow-2xl">
                        <img src="{{ asset('img/blog/blog-article-main.png') }}" alt="Crypto arbitrage"
                             class="w-full h-auto object-cover" />
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
            {{-- WHY DO PRICE DIFFERENCES OCCUR --}}
            <div class="mb-32">
                <img src="{{ asset('img/home/how-it-works-blue.svg') }}"
                     alt=""
                     class="absolute left-0 bottom-0 w-[820px] h-[820px] pointer-events-none select-none">

                <img src="{{ asset('img/home/how-it-works-yellow.svg') }}"
                     alt=""
                     class="absolute right-0 top-0 w-[1150px] h-[1150px] pointer-events-none select-none">
                <h2 class="text-4xl md:text-[70px] font-the-bold-font font-black text-[#3B4EFC] mb-16 uppercase leading-[1.07] tracking-tighter max-w-[900px]">
                    WHY DO PRICE DIFFERENCE OCCOUR ?
                </h2>

                <div class="grid lg:grid-cols-12 gap-8 items-start relative">
                    {{-- Liquidity Variations --}}
                    <div
                        class="lg:col-span-7
             bg-white/40 backdrop-blur-sm border border-[#2BA6FF]
             p-10 rounded-[18px]
             min-h-[240px] lg:min-h-[260px]
             flex flex-col justify-center
             shadow-[0_4px_20px_rgba(43,166,255,0.10)]"
                    >
                        <h3 class="text-3xl md:text-[50px] font-bold text-[#262262] mb-6 leading-[0.93]">
                            Liquidity Variations
                        </h3>
                        <p class="text-xl md:text-[28px] text-black/70 leading-normal max-w-[620px]">
                            Some exchanges have deeper order books and more trading activity than others.
                        </p>
                    </div>

                    {{-- Geographic Demand (поднимаем вверх на lg) --}}
                    <div
                        class="lg:col-span-5
             bg-white/40 backdrop-blur-sm border border-[#2BA6FF]
             p-10 rounded-[18px]
             min-h-[260px] lg:min-h-[300px]
             flex flex-col justify-center
             shadow-[0_4px_20px_rgba(43,166,255,0.10)]
             lg:translate-y-[-28px]"
                    >
                        <h3 class="text-3xl md:text-[50px] font-bold text-[#262262] mb-6 leading-[0.93]">
                            Geographic<br class="hidden md:block" /> Demand
                        </h3>
                        <p class="text-xl md:text-[28px] text-black/70 leading-normal max-w-[420px]">
                            Regional demand differences can influence pricing.
                        </p>
                    </div>

                    {{-- Transfer Delays (опускаем вниз на lg) --}}
                    <div
                        class="lg:col-span-5
             bg-white/40 backdrop-blur-sm border border-[#2BA6FF]
             p-10 rounded-[18px]
             min-h-[260px] lg:min-h-[320px]
             flex flex-col justify-center
             shadow-[0_4px_20px_rgba(43,166,255,0.10)]
             lg:translate-y-[20px]"
                    >
                        <h3 class="text-3xl md:text-[50px] font-bold text-[#262262] mb-6 leading-[0.93]">
                            Transfer<br class="hidden md:block" /> Delays
                        </h3>
                        <p class="text-xl md:text-[28px] text-black/70 leading-normal max-w-[420px]">
                            Network congestion can delay transactions, creating temporary price gaps.
                        </p>
                    </div>

                    {{-- Exchange Policies (чуть ниже/ровнее, широкая) --}}
                    <div
                        class="lg:col-span-7
             bg-white/40 backdrop-blur-sm border border-[#2BA6FF]
             p-10 rounded-[18px]
             min-h-[240px] lg:min-h-[300px]
             flex flex-col justify-center
             shadow-[0_4px_20px_rgba(43,166,255,0.10)]
             lg:translate-y-[10px]"
                    >
                        <h3 class="text-3xl md:text-[50px] font-bold text-[#262262] mb-6 leading-[0.93]">
                            Exchange Policies
                        </h3>
                        <p class="text-xl md:text-[28px] text-black/70 leading-normal max-w-[740px]">
                            Different fee structures, withdrawal restrictions, and trading limits impact price formation.
                        </p>
                    </div>
                </div>
            </div>

            {{-- WHAT IS CRYPTO ARBITRAGE --}}
            <div class="mb-32">
                <div class="bg-[#E0F2FF] border border-[#2BA6FF] rounded-[8px] p-12 md:p-20 relative overflow-hidden min-h-[700px] flex flex-col lg:flex-row items-center gap-12">
                    <div class="lg:w-1/2 relative z-10">
                        <h2 class="text-5xl md:text-[100px] font-the-bold-font font-black text-[#3B4EFC] uppercase leading-[1.04] tracking-tighter">
                            WHAT IS <br> CRYPTO <br> ARBITRAGE ?
                        </h2>
                    </div>
                    <div class="lg:w-1/2 space-y-8 relative z-10">
                        <p class="text-xl md:text-[26px] text-black leading-normal font-medium">
                            Crypto arbitrage is the practice of profiting from price discrepancies of the same digital asset across different exchanges or markets.
                        </p>
                        <ul class="list-disc pl-8 space-y-4 text-xl md:text-[28px] text-black leading-normal">
                            <li>Bitcoin may be trading at $28,000 on Exchange A.</li>
                            <li>At the same moment, it is priced at $28,500 on Exchange B.</li>
                        </ul>
                        <p class="text-xl md:text-[26px] text-black leading-normal">
                            An arbitrage trader purchases Bitcoin on Exchange A and sells it on Exchange B, securing the price difference after accounting for any fees.
                        </p>
                        <p class="text-xl md:text-[26px] text-black leading-normal">
                            This is fundamentally different from speculative trading. Arbitrage does not rely on predicting future price movements it is designed to exploit market inefficiencies for measurable, low-exposure returns.
                        </p>
                    </div>
                    {{-- Decorative image --}}
                    <div class="absolute right-[-50px] bottom-[-50px] opacity-30 lg:opacity-70 pointer-events-none">
                         <img src="{{ asset('img/blog/blog-what-is.png') }}" alt="" class="w-[547px] h-auto">
                    </div>
                </div>
            </div>

            {{-- CORE TYPES & ADVANTAGES --}}
            <div class="grid lg:grid-cols-2 gap-8 mb-32">
                {{-- Core Types --}}
                <div class="bg-gradient-to-b from-[#78A6DB]/80 to-[#E8F1FF] border-10 border-[#F5F5F5] rounded-[23px] p-12 shadow-[0_4px_10px_0_rgba(0,0,0,0.25)]">
                    <h2 class="text-3xl md:text-[40px] font-bold text-black/70 mb-8 uppercase tracking-[2px] leading-[1.08]">Core Types of Crypto Arbitrage</h2>
                    <p class="text-lg md:text-[20px] text-black mb-8">While there are many variations, the three primary forms are:</p>
                    <ol class="list-decimal pl-6 space-y-6 text-lg md:text-[18px] text-black/70">
                        <li><span class="font-bold text-black">Spatial Arbitrage</span> – Buying on one exchange and selling on another.</li>
                        <li><span class="font-bold text-black">Triangular Arbitrage</span> – Exploiting differences between three cryptocurrencies within a single exchange.</li>
                        <li><span class="font-bold text-black">Statistical Arbitrage</span> – Using algorithmic models to identify and execute on multiple small discrepancies across markets.</li>
                    </ol>
                </div>

                {{-- Advantages --}}
                <div class="bg-white border-10 border-[#F5F5F5] rounded-[23px] p-12 shadow-[0_4px_10px_0_rgba(0,0,0,0.25)] flex flex-col">
                    <h2 class="text-3xl md:text-[40px] font-bold text-black/70 mb-8 uppercase tracking-[2px] leading-[1.08]">Advantages of Arbitrage as an Investment Strategy</h2>
                    <p class="text-lg md:text-[24px] text-black mb-8">Arbitrage provides several advantages over traditional trading methods:</p>
                    <div class="space-y-6 text-lg md:text-[18px] text-black/70">
                        <p><span class="font-bold text-black block">Lower Market Risk:</span> Positions are typically opened and closed within seconds or minutes.</p>
                        <p><span class="font-bold text-black block">Consistent Potential Returns:</span> Profit is generated from inefficiencies, not market direction.</p>
                        <p><span class="font-bold text-black block">Market Neutrality:</span> Arbitrage remains effective in both rising and falling markets.</p>
                    </div>
                </div>
            </div>

            {{-- HOW WE SIMPLIFY & BARRIERS --}}
            <div class="grid lg:grid-cols-2 gap-8 mb-32">
                {{-- How Luma Stake Simplifies --}}
                <div class="bg-gradient-to-b from-[#A2D0BF] to-[#F5F5F5]/70 border-10 border-[#F5F5F5] rounded-[23px] p-12 shadow-[0_4px_10px_0_rgba(0,0,0,0.25)]">
                    <h2 class="text-3xl md:text-[40px] font-bold text-black/70 mb-8 uppercase tracking-[2px] leading-[1.08]">How Luma Stake Simplifies Arbitrage</h2>
                    <p class="text-lg md:text-[20px] text-black mb-8">Arbitex bridges the gap between opportunity and execution by combining professional-grade arbitrage strategies with a straightforward staking model:</p>
                    <ol class="list-decimal pl-6 space-y-6 text-lg md:text-[18px] text-black/70">
                        <li><span class="font-bold text-black">You Stake Your USDT</span> – Choose your preferred tier and staking period.</li>
                        <li><span class="font-bold text-black">We Execute Arbitrage</span> – Our system identifies and acts on price gaps using a risk-managed approach.</li>
                        <li><span class="font-bold text-black">You Earn Returns</span> – Enjoy consistent staking rewards without the need for active trading.</li>
                    </ol>
                    <p class="mt-8 text-lg md:text-[20px] text-black font-medium italic">Importantly, Luma Stake does not engage in futures or leveraged trading. Our approach is based entirely on real arbitrage, prioritizing stability, transparency, and risk control.</p>
                </div>

                {{-- The Barriers --}}
                <div class="bg-gradient-to-b from-[#F5F5F5] to-[#E3BCF4]/70 border-10 border-[#F5F5F5] rounded-[23px] p-12 shadow-[0_4px_10px_0_rgba(0,0,0,0.25)] flex flex-col relative">
                    <h2 class="text-3xl md:text-[40px] font-bold text-black/70 mb-8 uppercase tracking-[2px] leading-[1.08]">The Barriers to Manual Arbitrage</h2>
                    <p class="text-lg md:text-[20px] text-black mb-8">While the concept is straightforward, executing arbitrage manually is challenging. It requires:</p>
                    <ul class="list-disc pl-6 space-y-6 text-lg md:text-[18px] text-black/70">
                        <li><span class="font-bold text-black">Continuous market monitoring:</span> across multiple platforms.</li>
                        <li><span class="font-bold text-black">High-speed trade execution:</span> to capture fleeting opportunities.</li>
                        <li><span class="font-bold text-black">Precision in fee management:</span> to ensure trades remain profitable.</li>
                    </ul>
                    <p class="mt-8 text-lg md:text-[20px] text-black">For most individual investors, these demands make consistent arbitrage trading impractical.</p>
                    <div class="absolute right-10 bottom-10 opacity-20 pointer-events-none">
                        <img src="{{ asset('img/blog/8fcbf31a78f7015f8bcc0b690e635826abed2e00.png') }}" alt="" class="w-[100px] h-auto">
                    </div>
                </div>
            </div>

            {{-- CONCLUSION --}}
            <div class="mb-32">
                <div class="bg-white/47 border border-[#2BA6FF] rounded-[8px] p-12 relative shadow-[0_4px_4px_0_rgba(0,0,0,0.25)] flex flex-col justify-center min-h-[311px]">
                    <div class="absolute left-0 top-[40px] bottom-[40px] w-[6px] bg-[#2BA6FF] rounded-[6px]"></div>
                    <h2 class="text-4xl md:text-[40px] font-the-bold-font font-black text-[#3B4EFC] mb-8 uppercase leading-[0.9] ml-4 tracking-tighter">CONCLUSION</h2>
                    <div class="text-2xl md:text-[28px] text-[#22253B]/79 leading-normal ml-4 space-y-6">
                        <p>Crypto arbitrage remains one of the most reliable strategies in a volatile market, offering the potential for steady, low-risk returns. With Luma Stake, the complexities of arbitrage are handled for you, allowing you to benefit from proven market strategies while focusing on your broader investment goals.</p>
                        <p>Market inefficiencies will always exist — the key is having the expertise, speed, and systems to capture them. Luma Stake provides all three.</p>
                    </div>
                </div>
            </div>

            {{-- ARTICLES GRID (Section News moved down) --}}
            <div>
                <h2 class="text-4xl md:text-[60px] font-the-bold-font font-black text-[#3B4EFC] mb-12 uppercase leading-[0.9] tracking-tighter">
                    Latest News
                </h2>

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
        </div>
    </section>


@endsection
