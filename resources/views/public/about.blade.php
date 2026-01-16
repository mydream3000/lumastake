@extends('layouts.public')

@section('content')
    {{-- HERO SECTION --}}
    <section class="relative pt-10 pb-20 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 relative z-10">
            <div class="bg-gradient-to-b from-[#3B4EFC] to-[#95D2FF] rounded-[40px] p-12 md:p-20 relative overflow-hidden min-h-[600px] flex flex-col items-center justify-center text-center">
                {{-- Background Chart Image --}}
                <div class="absolute inset-0 opacity-40 mix-blend-overlay">
                    <img src="{{ asset('img/about/about-hero-bg.png') }}" alt="Chart" class="w-full h-full object-cover">
                </div>

                <h1 class="text-6xl md:text-8xl lg:text-9xl font-black text-white leading-none mb-8 relative z-10">
                    DIGITAL ASSETS
                </h1>

                <p class="text-xl md:text-3xl text-white max-w-4xl mx-auto leading-relaxed relative z-10">
                    At <span class="font-bold text-[#E3FF3B]">Luma Stake</span> we provide a secure, transparent staking solution for those who value consistency, clarity, and long-term growth.
                </p>
            </div>
        </div>
    </section>

    {{-- PROCESS / MISSION / VISION --}}
    <section class="py-12 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-3 gap-8">
            {{-- Process --}}
            <div class="bg-white border border-[#2BA6FF] rounded-[30px] p-10 shadow-[0_4px_4px_0_rgba(43,166,255,0.25)] flex flex-col items-center text-center">
                <div class="w-20 h-20 mb-6 bg-[#3B4EFC] rounded-full flex items-center justify-center">
                    <img src="{{ asset('img/about/icon-process.png') }}" alt="Process" class="w-12 h-12">
                </div>
                <h3 class="text-3xl font-black text-[#3B4EFC] mb-4 uppercase">Process</h3>
                <p class="text-gray-700 leading-relaxed">
                    Luma Stake bridges complex blockchain technology with practical wealth generation—using advanced staking and real-time automation to deliver stable, efficient earnings without speculation or constant market monitoring.
                </p>
            </div>

            {{-- Mission --}}
            <div class="bg-white border border-[#2BA6FF] rounded-[30px] p-10 shadow-[0_4px_4px_0_rgba(43,166,255,0.25)] flex flex-col items-center text-center">
                <div class="w-20 h-20 mb-6 bg-[#3B4EFC] rounded-full flex items-center justify-center">
                    <img src="{{ asset('img/about/icon-mission.png') }}" alt="Mission" class="w-12 h-12">
                </div>
                <h3 class="text-3xl font-black text-[#3B4EFC] mb-4 uppercase">Mission</h3>
                <p class="text-gray-700 leading-relaxed">
                    To provide a secure and structured environment where users can stake with confidence, earn passively, and grow their portfolios in a sustainable way.
                </p>
            </div>

            {{-- Vision --}}
            <div class="bg-white border border-[#2BA6FF] rounded-[30px] p-10 shadow-[0_4px_4px_0_rgba(43,166,255,0.25)] flex flex-col items-center text-center">
                <div class="w-20 h-20 mb-6 bg-[#3B4EFC] rounded-full flex items-center justify-center">
                    <img src="{{ asset('img/about/icon-vision.png') }}" alt="Vision" class="w-12 h-12">
                </div>
                <h3 class="text-3xl font-black text-[#3B4EFC] mb-4 uppercase">Vision</h3>
                <p class="text-gray-700 leading-relaxed">
                    To become a global leader in decentralized wealth solutions by consistently delivering results, building trust, and prioritizing user experience.
                </p>
            </div>
        </div>
    </section>

    {{-- WHY LUMA STAKE --}}
    <section class="py-24 bg-white overflow-hidden relative">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-6xl md:text-8xl font-black text-[#3B4EFC] mb-16 uppercase">
                WHY LUMA STAKE ?
            </h2>

            <div class="grid lg:grid-cols-2 gap-8 items-start">
                <div class="space-y-8">
                    {{-- Predictable Returns --}}
                    <div class="bg-white/50 backdrop-blur-sm border border-[#2BA6FF] p-10 rounded-2xl relative">
                        <h3 class="text-4xl font-black text-[#262262] mb-4">Predictable Returns</h3>
                        <p class="text-xl text-gray-700 leading-relaxed">
                            Our staking system is designed for performance, not promises. Earnings are based on clear terms and transparent structures.
                        </p>
                    </div>

                    {{-- User Centric Design --}}
                    <div class="bg-white/50 backdrop-blur-sm border border-[#2BA6FF] p-10 rounded-2xl">
                        <h3 class="text-4xl font-black text-[#262262] mb-4">User Centric Design</h3>
                        <p class="text-xl text-gray-700 leading-relaxed">
                            The Luma stake interface is built to remove confusion. With easy onboarding, straightforward tiers, and live earnings tracking, users stay in control at every step.
                        </p>
                    </div>
                </div>

                {{-- Capital Protection --}}
                <div class="bg-white/50 backdrop-blur-sm border border-[#2BA6FF] p-10 rounded-2xl lg:h-full flex flex-col justify-center">
                    <h3 class="text-4xl font-black text-[#262262] mb-4">Capital Protection</h3>
                    <p class="text-xl text-gray-700 leading-relaxed">
                        Security is our foundation. We follow strict protocols and maintain a risk-managed approach to staking.
                    </p>
                </div>
            </div>
        </div>

        {{-- Decorative Pyramid Background --}}
        <div class="absolute right-0 bottom-0 w-1/3 opacity-5 pointer-events-none">
            <img src="{{ asset('img/about/about-pyramid-light.png') }}" alt="" class="w-full">
        </div>
    </section>

    {{-- HOW WE GENERATE PROFIT --}}
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4">
            <div class="bg-gradient-to-r from-[#3B4EFC] to-[#95D2FF] rounded-[20px] p-8 text-center mb-12">
                <h2 class="text-4xl md:text-6xl font-black text-white uppercase">
                    HOW WE GENERATE PROFIT ?
                </h2>
            </div>

            <div class="max-w-5xl mx-auto text-center">
                <p class="text-2xl md:text-3xl text-gray-700 leading-relaxed">
                    We are a professional <span class="font-bold">arbitrage firm.</span> Staked funds are used in low-risk arbitrage trades that profit from price differences across platforms. This method works in any market up or down — as it relies on <span class="font-bold">volatility,</span> not direction. Profits are shared between Luma Stake and our users, powered by a team of experts with <span class="font-bold">10+ years of experience.</span>
                </p>
            </div>
        </div>
    </section>

    {{-- WHO WE SERVE --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="bg-white border border-[#2BA6FF] rounded-[20px] p-12 relative shadow-[0_4px_15px_rgba(0,0,0,0.1)]">
                <div class="absolute left-0 top-1/4 bottom-1/4 w-3 bg-[#3B4EFC] rounded-r-lg"></div>
                <h2 class="text-4xl font-black text-[#3B4EFC] mb-6 uppercase">Who we serve</h2>
                <p class="text-2xl text-gray-700 leading-relaxed">
                    Luma stake is trusted by working professionals, crypto enthusiasts, passive income seekers, and anyone who wants their capital to work harder — without unnecessary risk.
                </p>
            </div>
        </div>
    </section>
@endsection
