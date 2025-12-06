@extends('layouts.public')

@section('content')
<div class="bg-[#101221] min-h-screen relative">
    <!-- Hero Section -->
    <section class="relative pt-32 pb-16">
        <!-- Background decorative elements -->
        <div class="absolute right-0 top-64 w-96 h-96 opacity-20">
            <img src="{{ asset('assets/09af9496dcc2930ccbcbd9585a908dd018adf7b2.svg') }}" alt="" class="w-full h-full">
        </div>
        <div class="absolute right-20 top-40 w-80 h-80 opacity-15">
            <img src="{{ asset('assets/630ae6568f79e53581d752492d091b1828abdea6.svg') }}" alt="" class="w-full h-full">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h1 class="text-left text-white font-medium text-3xl md:text-5xl lg:text-5xl mb-4 md:mb-6">
                THE FUNDAMENTALS OF
            </h1>
            <h2 class="text-left text-4xl md:text-6xl lg:text-8xl font-extrabold mb-6 gradient-text bg-clip-text text-transparent">
                CRYPTO ARBITRAGE
            </h2>

            <div class="max-w-7xl text-left mx-auto text-white text-lg md:text-2xl lg:text-3xl leading-normal mb-12 md:mb-16">
                <p>
                    The cryptocurrency market operates around the clock, with prices fluctuating by the second. This constant movement creates opportunities for those who know where to look. Among the many strategies available,
                    <span class="text-[#ff00d8] font-semibold">crypto arbitrage</span>
                    stands out as a disciplined, market-neutral approach designed to capture consistent returns from temporary price inefficiencies.
                </p>
            </div>

            <div class="max-w-7xl text-left mx-auto text-white text-lg md:text-2xl lg:text-3xl leading-normal mb-16">
                <p>
                    At <span class="text-[#ff451c] font-semibold">Lumastake,</span> our staking model is built on the proven principles of arbitrage, making it possible for investors to benefit from these opportunities without the need for complex trading skills or constant market monitoring.
                </p>
            </div>

            <!-- Hero Image -->
            <div class="relative max-w-7xl mx-auto">
                <img src="{{ asset('assets/1c52997399d625664b6a087a2d0afd51f0331cd8.png') }}"
                     alt="Crypto Trading Platform"
                     class="w-full rounded-[33px] border border-[#00ffa3] shadow-2xl">
            </div>
        </div>
    </section>

    <!-- What is Crypto Arbitrage Section -->
    <section class="relative py-16 md:py-24">
        <!-- Background decorative elements -->
        <div class="absolute inset-0 opacity-10">
            <img src="{{ asset('assets/6ae07a9ca3683fa78be563523c72c2b0193fe9d1.png') }}"
                 alt=""
                 class="w-full h-full object-cover">
        </div>
        <div class="absolute left-0 top-32 w-96 h-96 opacity-20">
            <img src="{{ asset('assets/b31ede1f84c6234b57f5d4502f45a5769741b43e.svg') }}" alt="" class="w-full h-full">
        </div>
        <div class="absolute right-0 top-48 w-80 h-80 opacity-20">
            <img src="{{ asset('assets/42492c18089497fe89c40132b96dcd3f4afc822e.svg') }}" alt="" class="w-full h-full">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <h2 class="text-white font-extrabold text-4xl md:text-6xl lg:text-7xl text-center mb-12 md:mb-16 tracking-wider">
                WHAT IS CRYPTO ARBITRAGE?
            </h2>

            <div class="text-white text-xl md:text-2xl lg:text-3xl text-center mb-16 leading-normal">
                <p>Crypto arbitrage is the practice of profiting from price discrepancies of the same digital asset across different exchanges or markets.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-12 md:gap-16 items-center">
                <div class="text-white">
                    <h3 class="text-2xl md:text-3xl font-medium mb-8">For Example:</h3>
                    <ul class="space-y-6 text-xl md:text-2xl lg:text-3xl">
                        <li class="flex items-start">
                            <span class="text-[#00ffa3] mr-4">•</span>
                            Bitcoin may be trading at $28,000 on Exchange A.
                        </li>
                        <li class="flex items-start">
                            <span class="text-[#00ffa3] mr-4">•</span>
                            At the same moment, it is priced at $28,500 on Exchange B.
                        </li>
                    </ul>
                </div>
                <div class="relative">
                    <img src="{{ asset('assets/20d697570084f51395f453d0b59a2d2d82a759d0.png') }}"
                         alt="Security Image"
                         class="w-full max-w-md mx-auto">
                </div>
            </div>

            <div class="mt-16 space-y-8 text-white text-xl md:text-2xl lg:text-3xl text-center leading-normal">
                <p>An arbitrage trader purchases Bitcoin on Exchange A and sells it on Exchange B, securing the price difference after accounting for any fees.</p>

                <p>This is fundamentally different from speculative trading. Arbitrage does not rely on predicting future price movements — it is designed to exploit market inefficiencies for measurable, low-exposure returns.</p>
            </div>
        </div>
    </section>

    <!-- Why Do Price Differences Occur Section -->
    <section class="relative py-16 md:py-24">
        <!-- Background shapes -->
        <div class="absolute inset-0 opacity-20">
            <img src="{{ asset('assets/79c6d9577f460daaeef00bd849f331f6ad393baa.png') }}"
                 alt=""
                 class="w-full h-full object-cover">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <h2 class="text-[#ff00d8] font-extrabold text-4xl md:text-6xl lg:text-7xl text-center mb-12 md:mb-16 tracking-wider leading-tight">
                WHY DO PRICE<br class="md:hidden">
                DIFFERENCES OCCUR?
            </h2>

            <div class="text-white text-xl md:text-2xl lg:text-3xl text-center mb-16 leading-normal">
                <p>Even in a highly digital and connected market, price disparities persist due to:</p>
            </div>

            <div class="space-y-12">
                <div class="flex items-start space-x-6 relative">
                    <div class="line-between flex-shrink-0 w-1 bg-[#ff00d8] h-24 rounded"></div>
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 relative">
                            <img src="{{ asset('assets/7c7139d994cfafbde020504d0a4e8fddbe513567.svg') }}" alt="" class="absolute inset-0 w-full h-full">
                            <img src="{{ asset('assets/0f1db8416af53fb1eb72b45eb5a7174a9e48a649.svg') }}" alt="" class="absolute inset-2 w-12 h-12">
                        </div>
                    </div>
                    <div class="text-white">
                        <h3 class="text-2xl md:text-3xl font-bold mb-2">Liquidity Variations:</h3>
                        <p class="text-xl md:text-2xl leading-normal">Some exchanges have deeper order books and more trading activity than others.</p>
                    </div>
                </div>

                <div class="flex items-start space-x-6">
                    <div class="line-between flex-shrink-0 w-1 bg-[#ff00d8] h-24 rounded"></div>
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 relative">
                            <img src="{{ asset('assets/7c7139d994cfafbde020504d0a4e8fddbe513567.svg') }}" alt="" class="absolute inset-0 w-full h-full">
                            <img src="{{ asset('assets/0f1db8416af53fb1eb72b45eb5a7174a9e48a649.svg') }}" alt="" class="absolute inset-2 w-12 h-12">
                        </div>
                    </div>
                    <div class="text-white">
                        <h3 class="text-2xl md:text-3xl font-bold mb-2">Geographic Demand:</h3>
                        <p class="text-xl md:text-2xl leading-normal">Regional demand differences can influence pricing.</p>
                    </div>
                </div>

                <div class="flex items-start space-x-6">
                    <div class="line-between flex-shrink-0 w-1 bg-[#ff00d8] h-24 rounded"></div>
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 relative">
                            <img src="{{ asset('assets/7c7139d994cfafbde020504d0a4e8fddbe513567.svg') }}" alt="" class="absolute inset-0 w-full h-full">
                            <img src="{{ asset('assets/0f1db8416af53fb1eb72b45eb5a7174a9e48a649.svg') }}" alt="" class="absolute inset-2 w-12 h-12">
                        </div>
                    </div>
                    <div class="text-white">
                        <h3 class="text-2xl md:text-3xl font-bold mb-2">Transfer Delays:</h3>
                        <p class="text-xl md:text-2xl leading-normal">Network congestion can delay transactions, creating temporary price gaps.</p>
                    </div>
                </div>

                <div class="flex items-start space-x-6">
                    <div class="line-between flex-shrink-0 w-1 bg-[#ff00d8] h-24 rounded"></div>
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 relative">
                            <img src="{{ asset('assets/7c7139d994cfafbde020504d0a4e8fddbe513567.svg') }}" alt="" class="absolute inset-0 w-full h-full">
                            <img src="{{ asset('assets/0f1db8416af53fb1eb72b45eb5a7174a9e48a649.svg') }}" alt="" class="absolute inset-2 w-12 h-12">
                        </div>
                    </div>
                    <div class="text-white">
                        <h3 class="text-2xl md:text-3xl font-bold mb-2">Exchange Policies:</h3>
                        <p class="text-xl md:text-2xl leading-normal">Different fee structures, withdrawal restrictions, and trading limits impact price formation.</p>
                    </div>
                </div>
            </div>

            <div class="mt-16 text-white text-xl md:text-2xl lg:text-3xl text-center leading-normal">
                <p>These gaps may exist for only seconds, but when identified and acted upon quickly, they represent profitable opportunities.</p>
            </div>
        </div>
    </section>

    <!-- Core Types Section -->
    <section class="relative py-16 md:py-24">
        <!-- Decorative elements -->
        <div class="absolute left-0 bottom-0 w-96 h-96 opacity-20">
            <img src="{{ asset('assets/697420d5b0a4768de8808f84a12a0c62db6e1cf9.svg') }}" alt="" class="w-full h-full">
        </div>
        <div class="absolute left-0 top-32 w-64 h-64 opacity-20">
            <img src="{{ asset('assets/35a4df13c740ecb596cbd98c7b64c811dc659773.svg') }}" alt="" class="w-full h-full">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Core Types -->
            <div class="bg-[rgba(34,37,59,0.5)] border border-[rgba(217,217,217,0.42)] rounded-[31px] p-8 md:p-12 mb-16 relative">
                <div class="absolute left-0 top-12 w-4 h-32 bg-[#94c83e] rounded-r"></div>
                <h2 class="text-[#94c83e] font-extrabold text-3xl md:text-4xl lg:text-5xl tracking-wider mb-8">
                    Core Types of Crypto Arbitrage
                </h2>
                <div class="text-white text-lg md:text-xl lg:text-2xl mb-8 leading-normal">
                    <p>While there are many variations, the three primary forms are:</p>
                </div>
                <ol class="text-white text-lg md:text-xl lg:text-2xl space-y-6 leading-normal">
                    <li class="flex items-start">
                        <span class="text-[#94c83e] font-bold mr-4 flex-shrink-0">1.</span>
                        <div>
                            <span class="font-semibold">Spatial Arbitrage</span> – Buying on one exchange and selling on another.
                        </div>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#94c83e] font-bold mr-4 flex-shrink-0">2.</span>
                        <div>
                            <span class="font-semibold">Triangular Arbitrage</span> – Exploiting differences between three cryptocurrencies within a single exchange.
                        </div>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#94c83e] font-bold mr-4 flex-shrink-0">3.</span>
                        <div>
                            <span class="font-semibold">Statistical Arbitrage</span> – Using algorithmic models to identify and execute on multiple small discrepancies across markets.
                        </div>
                    </li>
                </ol>
            </div>

            <!-- Advantages vs Barriers -->
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Advantages -->
                <div class="bg-[rgba(34,37,59,0.5)] border border-[rgba(217,217,217,0.42)] rounded-[31px] p-8 relative">
                    <div class="absolute left-0 top-12 w-4 h-32 bg-[#ff451c] rounded-r"></div>
                    <h3 class="text-[#ff451c] font-extrabold text-2xl md:text-3xl lg:text-4xl tracking-wider mb-6">
                        Advantages of Arbitrage as an Investment Strategy
                    </h3>
                    <div class="text-white text-lg md:text-xl mb-6 leading-normal">
                        <p>Arbitrage provides several advantages over traditional trading methods:</p>
                    </div>
                    <ol class="text-white text-lg md:text-xl space-y-6 leading-normal">
                        <li class="flex items-start">
                            <span class="text-[#ff451c] font-bold mr-4 flex-shrink-0">1.</span>
                            <div>
                                <div class="font-semibold mb-1">Lower Market Risk:</div>
                                <div>Positions are typically opened and closed within seconds or minutes.</div>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="text-[#ff451c] font-bold mr-4 flex-shrink-0">2.</span>
                            <div>
                                <div class="font-semibold mb-1">Consistent Potential Returns:</div>
                                <div>Profit is generated from inefficiencies, not market direction.</div>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="text-[#ff451c] font-bold mr-4 flex-shrink-0">3.</span>
                            <div>
                                <div class="font-semibold mb-1">Market Neutrality:</div>
                                <div>Arbitrage remains effective in both rising and falling markets</div>
                            </div>
                        </li>
                    </ol>
                </div>

                <!-- Barriers -->
                <div class="bg-[rgba(34,37,59,0.5)] border border-[rgba(217,217,217,0.42)] rounded-[31px] p-8 relative">
                    <div class="absolute left-0 top-12 w-4 h-32 bg-[#ff00d8] rounded-r"></div>
                    <div class="absolute right-0 top-0 w-64 h-64 opacity-20">
                        <img src="{{ asset('assets/95939e8e1fc67371dbef4b8400cf5e844566b358.svg') }}" alt="" class="w-full h-full">
                    </div>
                    <h3 class="text-[#ff00d8] font-extrabold text-2xl md:text-3xl lg:text-4xl tracking-wider mb-6 relative z-10">
                        The Barriers to Manual Arbitrage
                    </h3>
                    <div class="text-white text-lg md:text-xl mb-6 leading-normal relative z-10">
                        <p>While the concept is straightforward, executing arbitrage manually is challenging. It requires:</p>
                    </div>
                    <ol class="text-white text-lg md:text-xl space-y-6 leading-normal relative z-10">
                        <li class="flex items-start">
                            <span class="text-[#ff00d8] font-bold mr-4 flex-shrink-0">1.</span>
                            <div>
                                <div class="font-semibold mb-1">Continuous market monitoring:</div>
                                <div>across multiple platforms.</div>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="text-[#ff00d8] font-bold mr-4 flex-shrink-0">2.</span>
                            <div>
                                <div class="font-semibold mb-1">High-speed trade execution</div>
                                <div>to capture fleeting opportunities.</div>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="text-[#ff00d8] font-bold mr-4 flex-shrink-0">3.</span>
                            <div>
                                <div class="font-semibold mb-1">Precision in fee management:</div>
                                <div>to ensure trades remain profitable.</div>
                            </div>
                        </li>
                    </ol>
                    <div class="mt-8 text-white text-lg md:text-xl leading-normal relative z-10">
                        <p>For most individual investors, these demands make consistent arbitrage trading impractical.</p>
                    </div>
                </div>
            </div>

            <!-- How Lumastake Simplifies -->
            <div class="bg-[rgba(34,37,59,0.5)] border border-[rgba(217,217,217,0.42)] rounded-[31px] p-8 md:p-12 mt-16 relative">
                <div class="absolute left-0 top-12 w-4 h-32 bg-[#00ffa3] rounded-r"></div>
                <h2 class="text-[#00ffa3] font-extrabold text-3xl md:text-4xl lg:text-5xl tracking-wider mb-8">
                    How Lumastake Simplifies Arbitrage
                </h2>
                <div class="text-white text-lg md:text-xl lg:text-2xl mb-8 leading-normal">
                    <p>Arbitex bridges the gap between opportunity and execution by combining professional-grade arbitrage strategies with a straightforward staking model:</p>
                </div>
                <ol class="text-white text-lg md:text-xl lg:text-2xl space-y-6 leading-normal mb-8">
                    <li class="flex items-start">
                        <span class="text-[#00ffa3] font-bold mr-4 flex-shrink-0">1.</span>
                        <div>
                            <span class="font-semibold">You Stake Your USDT</span> – Choose your preferred tier and staking period.
                        </div>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#00ffa3] font-bold mr-4 flex-shrink-0">2.</span>
                        <div>
                            <span class="font-semibold">We Execute Arbitrage</span> – Our system identifies and acts on price gaps using a risk-managed approach.
                        </div>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#00ffa3] font-bold mr-4 flex-shrink-0">3.</span>
                        <div>
                            <span class="font-semibold">You Earn Returns</span> – Enjoy consistent staking rewards without the need for active trading.
                        </div>
                    </li>
                </ol>
                <div class="text-white text-lg md:text-xl lg:text-2xl leading-normal">
                    <p>Importantly, Lumastake does not engage in futures or leveraged trading. Our approach is based entirely on real arbitrage, prioritizing stability, transparency, and risk control.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Conclusion Section -->
    <section class="relative py-16 md:py-24">
        <!-- Gradient background -->
        <div class="absolute inset-0 bg-gradient-to-r from-[#00ffa3] via-[#ff00d8] to-[#ff451c] opacity-90"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-white font-bold text-5xl md:text-8xl lg:text-9xl leading-[0.8]" style="font-family: 'Audiowide', cursive;">
                CONCLUSION
            </h2>


        </div>
    </section>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-left mt-6 relative z-10">
        <div class="space-y-8 text-white text-xl md:text-2xl lg:text-3xl leading-normal mb-16">
            <p>Crypto arbitrage remains one of the most reliable strategies in a volatile market, offering the potential for steady, low-risk returns. With Lumastake, the complexities of arbitrage are handled for you, allowing you to benefit from proven market strategies while focusing on your broader investment goals.</p>

            <p>Market inefficiencies will always exist — the key is having the expertise, speed, and systems to capture them. Lumastake provides all three.</p>
        </div>

        <div class="mb-12">
            <h3 class="bg-gradient-to-r from-[#00ffa3] via-[#ff00d8] to-[#ff451c] bg-clip-text text-transparent font-bold text-5xl md:text-7xl lg:text-8xl mb-8 leading-[0.8]" style="font-family: 'Audiowide', cursive;">
                STAKE SMARTER
            </h3>
            <div class="text-white font-extrabold text-2xl md:text-4xl lg:text-5xl tracking-wide leading-tight">
                GROW STEADILY. EXPERIENCE THE<br>
                POWER OF ARBITRAGE WITH LUMASTAKE
            </div>
        </div>
    </div>
    <!-- News Section -->
    @if($posts->count() > 0)
    <section class="relative py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <h2 class="text-white font-extrabold text-4xl md:text-6xl lg:text-7xl text-center mb-12 md:mb-16 tracking-wider">
                NEWS
            </h2>

            <div class="space-y-8">
                @foreach($posts as $post)
                <div class="bg-[rgba(34,37,59,0.5)] border border-[{{ $post->color_scheme }}] rounded-[31px] p-8 md:p-12 relative">
                    <!-- Mini color block -->
                    <div class="absolute left-0 top-12 w-4 h-32 bg-[{{ $post->color_scheme }}] rounded-r"></div>

                    <div class="grid md:grid-cols-2 gap-8 items-center">
                        <!-- Text content (left) -->
                        <div class="text-white">
                            <h3 class="text-2xl md:text-3xl font-bold mb-4" style="color: {{ $post->color_scheme }};">
                                {{ $post->title }}
                            </h3>

                            <div class="mb-4 text-sm text-gray-400">
                                <span>{{ $post->published_at->format('d, M, Y') }}</span>
                                @if($post->author)
                                    <span class="mx-2">•</span>
                                    <span>By {{ $post->author->name }}</span>
                                @endif
{{--                                <span class="mx-2">•</span>--}}
{{--                                <span>{{ $post->views }} views</span>--}}
                            </div>

                            <div class="text-white text-base leading-relaxed mb-6" style="font-size: 15px; max-height: 7.5em; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 5; -webkit-box-orient: vertical;">
                                {{ $post->excerpt }}
                            </div>

                            <a href="{{ route('blog.show', $post->slug) }}"
                               class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-medium transition-colors"
                               style="background-color: {{ $post->color_scheme }}; color: #101221;">
                                Read More
                                <i class="fas fa-chevron-right text-sm"></i>
                            </a>
                        </div>

                        <!-- Image (right) -->
                        <div class="justify-center mx-auto order-first md:order-last">
                            @if($post->image)
                                <img src="{{ asset($post->image) }}"
                                     alt="{{ $post->title }}"
                                     class="w-80 h-64 md:h-80 object-cover rounded-2xl">
                            @else
                                <div class="w-full h-64 md:h-80 bg-gray-700 rounded-2xl flex items-center justify-center">
                                    <i class="fas fa-image text-gray-500 text-6xl"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($posts->hasPages())
            <div class="mt-12 flex justify-center">
                <div class="flex gap-2">
                    {{-- Previous Page Link --}}
                    @if ($posts->onFirstPage())
                        <span class="px-4 py-2 bg-gray-700 text-gray-500 rounded-lg cursor-not-allowed">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $posts->previousPageUrl() }}" class="px-4 py-2 bg-[#00ffa3] text-[#101221] rounded-lg hover:bg-[#00ffa3]/80 transition-colors">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach(range(1, $posts->lastPage()) as $page)
                        @if ($page == $posts->currentPage())
                            <span class="px-4 py-2 bg-[#00ffa3] text-[#101221] rounded-lg font-bold">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $posts->url($page) }}" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($posts->hasMorePages())
                        <a href="{{ $posts->nextPageUrl() }}" class="px-4 py-2 bg-[#00ffa3] text-[#101221] rounded-lg hover:bg-[#00ffa3]/80 transition-colors">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    @else
                        <span class="px-4 py-2 bg-gray-700 text-gray-500 rounded-lg cursor-not-allowed">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </section>
    @endif
</div>
@endsection
