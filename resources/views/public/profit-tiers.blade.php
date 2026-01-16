@extends('layouts.public')

@section('content')
    {{-- HERO SECTION --}}
    <section class="relative pt-10 pb-20 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 relative z-10">
            <div class="bg-gradient-to-b from-[#3B4EFC] to-[#95D2FF] rounded-[40px] p-12 md:p-20 relative overflow-hidden min-h-[600px] flex flex-col items-center justify-center">
                {{-- Background Chart Image --}}
                <div class="absolute inset-0 opacity-40 mix-blend-overlay">
                    <img src="{{ asset('img/tiers/tiers-hero-bg.png') }}" alt="Chart" class="w-full h-full object-cover">
                </div>

                <div class="relative w-full h-full flex flex-col lg:flex-row items-center justify-center gap-12 lg:gap-24">
                    {{-- Left Card --}}
                    <div class="bg-white border border-[#2BA6FF] rounded-[30px] p-10 shadow-[0_4px_4px_0_rgba(43,166,255,0.25)] transform -rotate-2 lg:-rotate-6 max-w-md text-center hover:rotate-0 transition-transform duration-500">
                        <h2 class="text-4xl md:text-5xl font-black text-[#3B4EFC] mb-4 leading-tight uppercase">
                            Rise Through <br> <span class="text-6xl md:text-7xl">Every Stake</span>
                        </h2>
                        <p class="text-xl text-[#262262] leading-relaxed font-medium">
                            Your Journey to Smarter Crypto Earnings Starts Here with Luma Stake
                        </p>
                    </div>

                    {{-- Right Card --}}
                    <div class="bg-white border border-[#2BA6FF] rounded-[30px] p-10 shadow-[0_4px_4px_0_rgba(43,166,255,0.25)] transform rotate-2 lg:rotate-6 max-w-md text-center hover:rotate-0 transition-transform duration-500">
                        <h2 class="text-4xl md:text-5xl font-black text-[#3B4EFC] mb-4 leading-tight uppercase">
                            Dynamic <br> <span class="text-6xl md:text-7xl">Earning Tiers</span>
                        </h2>
                        <p class="text-xl text-[#262262] leading-relaxed font-medium">
                            Maximize Returns at Every Level of Your Luma Stake Staking Experience
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- PROFIT TIERS SECTION --}}
    <section class="py-20 bg-white" id="tiers">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h2 class="text-6xl font-black text-[#262262] mb-6 uppercase">Profit Tiers</h2>
            <p class="text-2xl text-gray-600 max-w-4xl mx-auto mb-16 leading-relaxed font-medium">
                Our staking model is designed to reward commitment and consistency. The longer you stake, the higher your earning potential without active trading or market speculation.
            </p>

            {{-- Account Type Switcher --}}
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4 mb-20">
                <a href="{{ route('profit-tiers', ['type' => 'normal']) }}#tiers"
                   class="w-full sm:w-64 py-4 rounded-lg text-2xl font-black transition-all border-2 {{ $accountType === 'normal' ? 'bg-[#D9FF00] border-[#D9FF00] text-[#262262] shadow-lg' : 'bg-white border-gray-200 text-gray-400 hover:border-gray-300' }}">
                    Normal Account
                </a>
                <a href="{{ route('profit-tiers', ['type' => 'islamic']) }}#tiers"
                   class="w-full sm:w-64 py-4 rounded-lg text-2xl font-black transition-all border-2 {{ $accountType === 'islamic' ? 'bg-[#D9FF00] border-[#D9FF00] text-[#262262] shadow-lg' : 'bg-white border-gray-200 text-gray-400 hover:border-gray-300' }}">
                    Islamic Account
                </a>
            </div>

            {{-- Tiers Grid --}}
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10">
                @foreach($tiers as $tier)
                    @php
                        $percentages = $accountType === 'islamic' ? $tier->islamicPercentages : $tier->percentages;
                    @endphp
                    <div class="bg-white border border-[#2BA6FF] rounded-[40px] p-10 shadow-[0_4px_15px_rgba(43,166,255,0.1)] flex flex-col relative overflow-hidden group hover:shadow-[0_15px_30px_rgba(43,166,255,0.2)] transition-all duration-300 border-t-8 border-t-[#3B4EFC]">
                        <h3 class="text-4xl font-black text-[#3B4EFC] mb-4 uppercase text-left">
                            {{ $tier->name }}
                        </h3>

                        <div class="inline-block px-4 py-2 border-2 border-[#3B4EFC] text-[#3B4EFC] font-black text-xl mb-10 text-left self-start rounded">
                            @if($tier->max_balance)
                                ${{ number_format($tier->min_balance, 0) }} – ${{ number_format($tier->max_balance, 0) }} USD
                            @else
                                ${{ number_format($tier->min_balance, 0) }}+ USD
                            @endif
                        </div>

                        <div class="space-y-4 flex-grow">
                            @foreach($percentages as $p)
                                <div class="flex justify-between items-center py-2 border-b border-blue-50 last:border-0">
                                    <span class="text-gray-500 font-bold uppercase text-lg">
                                        {{ $accountType === 'islamic' ? $p->duration_days : $p->days }} Days
                                    </span>
                                    <span class="text-[#262262] font-black text-2xl">
                                        {{ number_format($p->percentage, 1) }}%
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-10">
                            <a href="{{ route('register') }}" class="block w-full py-4 rounded-2xl bg-[#3B4EFC] text-white text-xl font-black hover:bg-[#262262] transition-colors shadow-lg">
                                START STAKING
                            </a>
                        </div>
                    </div>
                @endforeach
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
                    <p class="text-2xl text-gray-700 leading-relaxed font-medium">
                        Join thousands of users who trust Luma Stake for their passive income. Secure, transparent, and built for your growth.
                    </p>
                </div>
                <div class="md:w-1/3 flex justify-center relative z-10">
                    <a href="{{ route('register') }}" class="bg-[#D9FF00] text-[#262262] text-3xl font-black px-12 py-6 rounded-2xl hover:scale-105 transition-transform shadow-xl uppercase text-center">
                        Join Now
                    </a>
                </div>

                {{-- Decorative background element --}}
                <div class="absolute right-0 bottom-0 opacity-10 pointer-events-none">
                    <img src="{{ asset('img/about/about-pyramid-light.png') }}" alt="" class="w-96">
                </div>
            </div>
        </div>
    </section>
@endsection
