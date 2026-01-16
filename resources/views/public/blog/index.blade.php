@extends('layouts.public')

@section('content')
    {{-- HERO SECTION --}}
    <section class="relative pt-10 pb-20 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 relative z-10">
            <div class="bg-gradient-to-b from-[#3B4EFC] to-[#95D2FF] rounded-[40px] p-12 md:p-20 relative overflow-hidden flex flex-col items-center text-center">
                {{-- Background Chart Image --}}
                <div class="absolute inset-0 opacity-20 mix-blend-overlay">
                    <img src="{{ asset('img/blog/blog-hero-bg.png') }}" alt="Chart" class="w-full h-full object-cover">
                </div>

                <h3 class="text-3xl md:text-5xl font-black text-white mb-4 uppercase relative z-10">
                    THE FUNDAMENTALS OF
                </h3>
                <h1 class="text-6xl md:text-8xl lg:text-9xl font-black text-white leading-none mb-10 relative z-10 uppercase">
                    CRYPTO ARBITRAGE
                </h1>

                <div class="max-w-5xl mx-auto relative z-10">
                    <p class="text-lg md:text-2xl text-white leading-relaxed mb-6">
                        The cryptocurrency market operates around the clock, with prices fluctuating by the second. This constant movement creates opportunities for those who know where to look. Among the many strategies available, <span class="text-[#E3FF3B] font-bold">crypto arbitrage</span> stands out as a disciplined, market-neutral approach designed to capture consistent returns from temporary price inefficiencies.
                    </p>
                </div>
            </div>

            {{-- Main Article Image --}}
            <div class="mt-[-80px] md:mt-[-120px] relative z-20 max-w-6xl mx-auto">
                <div class="bg-white border border-[#2BA6FF] rounded-[40px] p-2 shadow-[0_10px_30px_rgba(43,166,255,0.2)] overflow-hidden">
                    <img src="{{ asset('img/blog/blog-article-main.png') }}" alt="Crypto Arbitrage Fundamentals" class="w-full h-auto rounded-[36px]">
                </div>
            </div>
        </div>
    </section>

    {{-- WHY DO PRICE DIFFERENCE OCCOUR SECTION --}}
    <section class="py-20 bg-white relative overflow-hidden">
        {{-- Decorative background elements --}}
        <div class="absolute top-0 right-0 w-1/3 opacity-5 pointer-events-none">
            <img src="{{ asset('img/blog/blog-pyramid-light.png') }}" alt="" class="w-full">
        </div>

        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-5xl md:text-7xl font-black text-[#3B4EFC] mb-16 uppercase max-w-2xl leading-tight">
                WHY DO PRICE DIFFERENCE OCCOUR ?
            </h2>

            <div class="grid md:grid-cols-2 gap-8">
                @if($faqs->count() > 0)
                    @foreach($faqs as $faq)
                        <div class="bg-white/70 backdrop-blur-sm border border-[#2BA6FF] p-10 rounded-2xl shadow-[0_4px_15px_rgba(43,166,255,0.1)] hover:shadow-[0_10px_25px_rgba(43,166,255,0.2)] transition-all">
                            <h3 class="text-3xl md:text-4xl font-black text-[#262262] mb-6 leading-tight">{{ $faq->question }}</h3>
                            <p class="text-xl text-gray-700 leading-relaxed">{{ $faq->answer }}</p>
                        </div>
                    @endforeach
                @else
                    {{-- Fallback --}}
                    <div class="bg-white/70 backdrop-blur-sm border border-[#2BA6FF] p-10 rounded-2xl shadow-[0_4px_15px_rgba(43,166,255,0.1)]">
                        <h3 class="text-4xl font-black text-[#262262] mb-4">Liquidity Variations</h3>
                        <p class="text-xl text-gray-700 leading-relaxed">Some exchanges have deeper order books and more trading activity than others.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- WHAT IS CRYPTO ARBITRAGE SECTION --}}
    <section class="py-20 bg-[#E0F2FF]">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="space-y-8">
                    <h2 class="text-5xl md:text-7xl font-black text-[#3B4EFC] leading-none uppercase">
                        WHAT IS <br> CRYPTO ARBITRAGE ?
                    </h2>
                    <p class="text-2xl text-gray-800 leading-relaxed">
                        Crypto arbitrage is the practice of profiting from price discrepancies of the same digital asset across different exchanges or markets.
                    </p>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <span class="text-[#3B4EFC] mr-4 text-3xl font-black">•</span>
                            <span class="text-xl font-medium text-[#262262]">Bitcoin may be trading at $28,000 on Exchange A.</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-[#3B4EFC] mr-4 text-3xl font-black">•</span>
                            <span class="text-xl font-medium text-[#262262]">At the same moment, it is priced at $28,500 on Exchange B.</span>
                        </li>
                    </ul>
                    <p class="text-xl text-gray-700 leading-relaxed">
                        An arbitrage trader purchases Bitcoin on Exchange A and sells it on Exchange B, securing the price difference after accounting for any fees.
                    </p>
                    <p class="text-xl text-gray-700 font-medium">
                        This is fundamentally different from speculative trading. Arbitrage does not rely on predicting future price movements — it is designed to exploit market inefficiencies for measurable, low-exposure returns.
                    </p>
                </div>
                <div class="relative">
                    <img src="{{ asset('img/blog/blog-what-is.png') }}" alt="What is Crypto Arbitrage" class="w-full h-auto">
                </div>
            </div>
        </div>
    </section>

    {{-- TYPES AND ADVANTAGES SECTION --}}
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-12">
                {{-- Core Types --}}
                <div class="bg-gradient-to-b from-[#78A6DB]/80 to-[#E8F1FF] p-12 rounded-[40px] border-8 border-white shadow-xl">
                    <h3 class="text-4xl font-black text-[#262262] mb-8 leading-tight">Core Types of Crypto Arbitrage</h3>
                    <p class="text-xl text-gray-700 mb-8">While there are many variations, the three primary forms are:</p>
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <img src="{{ asset('img/blog/icon-spatial.png') }}" alt="" class="w-12 h-12 flex-shrink-0">
                            <div>
                                <h4 class="text-xl font-bold text-[#262262]">Spatial Arbitrage</h4>
                                <p class="text-gray-600 italic">Buying on one exchange and selling on another.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <img src="{{ asset('img/blog/icon-triangular.png') }}" alt="" class="w-12 h-12 flex-shrink-0">
                            <div>
                                <h4 class="text-xl font-bold text-[#262262]">Triangular Arbitrage</h4>
                                <p class="text-gray-600 italic">Exploiting differences between three cryptocurrencies within a single exchange.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <img src="{{ asset('img/blog/icon-statistical.png') }}" alt="" class="w-12 h-12 flex-shrink-0">
                            <div>
                                <h4 class="text-xl font-bold text-[#262262]">Statistical Arbitrage</h4>
                                <p class="text-gray-600 italic">Using algorithmic models to identify and execute on multiple small discrepancies across markets.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Advantages --}}
                <div class="bg-gradient-to-b from-white to-[#EAFF6D]/70 p-12 rounded-[40px] border-8 border-white shadow-xl">
                    <h3 class="text-4xl font-black text-[#262262] mb-8 leading-tight">Advantages of Arbitrage as an Investment Strategy</h3>
                    <p class="text-xl text-gray-700 mb-8">Arbitrage provides several advantages over traditional trading methods:</p>
                    <ul class="space-y-6">
                        <li class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-[#3B4EFC] flex items-center justify-center text-white font-bold flex-shrink-0">1</div>
                            <div>
                                <h4 class="text-xl font-bold text-[#262262]">Lower Market Risk:</h4>
                                <p class="text-gray-600">Positions are typically opened and closed within seconds or minutes.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-[#3B4EFC] flex items-center justify-center text-white font-bold flex-shrink-0">2</div>
                            <div>
                                <h4 class="text-xl font-bold text-[#262262]">Consistent Potential Returns:</h4>
                                <p class="text-gray-600">Profit is generated from inefficiencies, not market direction.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-[#3B4EFC] flex items-center justify-center text-white font-bold flex-shrink-0">3</div>
                            <div>
                                <h4 class="text-xl font-bold text-[#262262]">Market Neutrality:</h4>
                                <p class="text-gray-600">Arbitrage remains effective in both rising and falling markets.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- SIMPLIFIES AND BARRIERS SECTION --}}
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-12">
                {{-- How Luma Stake Simplifies --}}
                <div class="bg-gradient-to-b from-[#A2D0BF] to-white/70 p-12 rounded-[40px] border-8 border-[#F5F5F5] shadow-xl">
                    <h3 class="text-4xl font-black text-[#262262] mb-8 leading-tight">How Luma Stake Simplifies Arbitrage</h3>
                    <p class="text-xl text-gray-700 mb-8">Luma Stake bridges the gap between opportunity and execution by combining professional-grade arbitrage strategies with a straightforward staking model:</p>
                    <ul class="space-y-4 text-lg">
                        <li class="flex items-start gap-3">
                            <span class="font-bold text-[#262262]">1. You Stake Your USDT</span> – Choose your preferred tier and staking period.
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="font-bold text-[#262262]">2. We Execute Arbitrage</span> – Our system identifies and acts on price gaps using a risk-managed approach.
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="font-bold text-[#262262]">3. You Earn Returns</span> – Enjoy consistent staking rewards without the need for active trading.
                        </li>
                    </ul>
                    <p class="mt-8 text-gray-600 italic">
                        Importantly, Luma Stake does not engage in futures or leveraged trading. Our approach is based entirely on real arbitrage, prioritizing stability, transparency, and risk control.
                    </p>
                </div>

                {{-- Barriers --}}
                <div class="bg-gradient-to-b from-[#F5F5F5] to-[#E3BCF4]/70 p-12 rounded-[40px] border-8 border-[#F5F5F5] shadow-xl relative">
                    <h3 class="text-4xl font-black text-[#262262] mb-8 leading-tight">The Barriers to Manual Arbitrage</h3>
                    <p class="text-xl text-gray-700 mb-8">While the concept is straightforward, executing arbitrage manually is challenging. It requires:</p>
                    <ul class="space-y-6">
                        <li class="flex items-center gap-4">
                            <div class="w-8 h-8 rounded-full bg-[#3B4EFC] flex items-center justify-center text-white flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <p class="text-xl font-medium text-gray-700">Continuous market monitoring</p>
                        </li>
                        <li class="flex items-center gap-4">
                            <div class="w-8 h-8 rounded-full bg-[#3B4EFC] flex items-center justify-center text-white flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <p class="text-xl font-medium text-gray-700">High-speed trade execution</p>
                        </li>
                        <li class="flex items-center gap-4">
                            <div class="w-8 h-8 rounded-full bg-[#3B4EFC] flex items-center justify-center text-white flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <p class="text-xl font-medium text-gray-700">Precision in fee management</p>
                        </li>
                    </ul>
                    <p class="mt-8 text-xl font-bold text-[#262262]">
                        For most individual investors, these demands make consistent arbitrage trading impractical.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- CONCLUSION SECTION --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="bg-white border border-[#2BA6FF] rounded-[20px] p-12 relative shadow-[0_4px_15px_rgba(0,0,0,0.1)]">
                <div class="absolute left-0 top-1/4 bottom-1/4 w-3 bg-[#3B4EFC] rounded-r-lg"></div>
                <h2 class="text-4xl font-black text-[#3B4EFC] mb-6 uppercase">CONCLUSION</h2>
                <div class="space-y-6">
                    <p class="text-2xl text-gray-700 leading-relaxed">
                        Crypto arbitrage remains one of the most reliable strategies in a volatile market, offering the potential for steady, low-risk returns. With Luma Stake, the complexities of arbitrage are handled for you, allowing you to benefit from proven market strategies while focusing on your broader investment goals.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- FINAL CTA SECTION --}}
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="bg-[#E0F2FF] rounded-[40px] p-16 flex flex-col md:flex-row items-center justify-between gap-12 relative overflow-hidden">
                <div class="md:w-2/3 relative z-10">
                    <h2 class="text-5xl md:text-7xl font-black text-[#262262] leading-none mb-8 uppercase">
                        SECURE YOUR <br> <span class="text-[#3B4EFC]">FUTURE TODAY</span>
                    </h2>
                </div>
                <div class="md:w-1/3 flex justify-center relative z-10">
                    <a href="{{ route('register') }}" class="bg-[#D9FF00] text-[#262262] text-3xl font-black px-12 py-6 rounded-2xl hover:scale-105 transition-transform shadow-xl uppercase text-center">
                        Join Now
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
