@extends('layouts.public')

@section('content')
    {{-- HERO SECTION --}}
    <section class="relative pt-10 pb-20 overflow-hidden bg-white">
        <div class="max-w-[1440px] mx-auto px-4 md:px-12 relative z-10">
            <div class="bg-gradient-to-b from-[#3B4EFC] to-[#95D2FF] rounded-[34px] p-12 md:p-24 relative overflow-hidden min-h-[700px] flex flex-col items-center justify-center">
                {{-- Background Chart Image --}}
                <div class="absolute inset-0 opacity-40 mix-blend-overlay pointer-events-none">
                    <img src="{{ asset('img/about/about-hero-bg.png') }}" alt="Chart" class="w-full h-full object-cover">
                </div>

                <div class="relative w-full h-full flex flex-col lg:flex-row items-center justify-center gap-2 lg:gap-2">
                    {{-- Left Card --}}
                    <div class="bg-white border border-[#2BA6FF] rounded-[30px] p-10 shadow-[0_4px_4px_0_rgba(43,166,255,1)] transform -rotate-[6deg] lg:-rotate-[15deg] max-w-[440px] text-center hover:rotate-0 transition-transform duration-500 cursor-default">
                        <h2 class="text-4xl md:text-[50px] font-the-bold-font font-black text-[#3B4EFC] mb-4 leading-[0.9] uppercase tracking-tighter">
                            Rise Through <br> <span class="text-5xl md:text-[75px]">Every Stake</span>
                        </h2>
                        <p class="text-[20px] text-[#262262] leading-relaxed font-medium mt-6">
                            Your Journey to Smarter Crypto Earnings Starts Here with Luma Stake
                        </p>
                    </div>

                    {{-- Right Card --}}
                    <div class="bg-white border border-[#2BA6FF] rounded-[30px] p-10 shadow-[0_4px_4px_0_rgba(43,166,255,1)] transform rotate-[6deg] lg:rotate-[18deg] max-w-[440px] text-center hover:rotate-0 transition-transform duration-500 cursor-default">
                        <h2 class="text-4xl md:text-[50px] font-the-bold-font font-black text-[#3B4EFC] mb-4 leading-[0.9] uppercase tracking-tighter">
                            Dynamic <br> <span class="text-5xl md:text-[70px]">Earning Tiers</span>
                        </h2>
                        <p class="text-[20px] text-[#262262] leading-relaxed font-medium mt-6">
                            Maximize Returns at Every Level of Your Luma Stake Staking Experience
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- PROFIT TIERS SECTION --}}
    <section class="py-20 bg-white relative overflow-hidden" id="tiers">
        {{-- Decorative Elements --}}
        <div class="absolute left-[-200px] top-[10%] w-[600px] h-[600px] bg-[#3B4EFC]/5 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute right-[-200px] bottom-[20%] w-[600px] h-[600px] bg-[#3B4EFC]/5 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="max-w-[1440px] mx-auto px-4 md:px-12 text-center relative z-10">
            <h2 class="text-5xl md:text-[80px] font-the-bold-font font-black text-[#262262] mb-12 uppercase leading-[0.9] tracking-tighter">Profit Tiers</h2>

            <div class="max-w-[1036px] mx-auto mb-16">
                <p class="text-xl md:text-[24px] text-black/70 leading-normal">
                    Our staking model is designed to reward commitment and consistency. The longer you stake, the higher your earning potential without active trading or market speculation.
                </p>
            </div>

            {{-- Account Type Switcher --}}
            <div class="flex flex-col sm:flex-row justify-center items-center gap-6 mb-24">
                <a href="{{ route('profit-tiers', ['type' => 'normal']) }}#tiers"
                   class="w-full sm:w-[353px] py-4 rounded-lg text-[28px] font-bold transition-all border-2 {{ $accountType === 'normal' ? 'bg-[#D9FF00] border-[#D9FF00] text-black shadow-lg' : 'bg-white border-gray-200 text-gray-400 hover:border-gray-300' }}">
                    Normal Account
                </a>
                <a href="{{ route('profit-tiers', ['type' => 'islamic']) }}#tiers"
                   class="w-full sm:w-[353px] py-4 rounded-lg text-[28px] font-bold transition-all border-2 {{ $accountType === 'islamic' ? 'bg-[#D9FF00] border-[#D9FF00] text-black shadow-lg' : 'bg-white border-gray-200 text-gray-400 hover:border-gray-300' }}">
                    Islamic Account
                </a>
            </div>

            {{-- Tiers Grid --}}
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @php
                    $tiersCount = count($tiers);
                    $centerLast = $tiersCount % 3 === 1; // для lg:grid-cols-3
                @endphp
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($tiers as $tier)
                        @php
                            $isBlue = $loop->index % 2 !== 0;
                            $percentages = $accountType === 'islamic'
                                ? $tier->islamicPercentages
                                : $tier->percentages;

                            $moveToCenter = $loop->last && $centerLast;
                        @endphp

                        <div
                            class="
                {{ $isBlue
                    ? 'bg-gradient-to-b from-[#3B4EFC] to-[#95D2FF] text-white'
                    : 'bg-white border border-[#2BA6FF] text-[#3B4EFC]' }}

                {{ $moveToCenter ? 'lg:col-start-2' : '' }}

                rounded-[30px] p-10 shadow-[0_4px_4px_0_rgba(43,166,255,1)]
                flex flex-col relative transition-transform hover:scale-[1.02]
                min-h-[500px]
            "
                        >

                        <h3 class="text-[40px] font-the-bold-font font-black mb-6 uppercase leading-[0.9] text-left tracking-tighter">
                            {{ $tier->name }}
                        </h3>

                        <div class="inline-block px-4 py-2 border-2 {{ $isBlue ? 'border-white/50 text-white' : 'border-[#3B4EFC] text-[#3B4EFC]' }} font-bold text-[20px] mb-12 text-left self-start rounded-[4px] tracking-tight">
                            @if($tier->max_balance)
                                ${{ number_format($tier->min_balance, 0) }} – ${{ number_format($tier->max_balance, 0) }} USD
                            @else
                                ${{ number_format($tier->min_balance, 0) }}+ USD
                            @endif
                        </div>

                        <div class="space-y-6 flex-grow">
                            @foreach($percentages as $p)
                                <div class="flex justify-between items-center pb-2 {{ $isBlue ? 'border-b border-white/20' : 'border-b border-[#2BA6FF]/20' }} last:border-0">
                                    <span class="{{ $isBlue ? 'text-white' : 'text-black' }} font-bold uppercase text-[18px]">
                                        {{ $accountType === 'islamic' ? $p->duration_days : $p->days }} Days
                                    </span>
                                    <span class="{{ $isBlue ? 'text-white' : 'text-[#262262]' }} font-black text-[24px]">
                                        @if($accountType === 'islamic')
                                            {{ number_format($p->min_percentage, 1) }}% – {{ number_format($p->max_percentage, 1) }}%
                                        @else
                                            {{ number_format($p->percentage, 1) }}%
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

@endsection
