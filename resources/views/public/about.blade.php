@extends('layouts.public')

@section('content')
    {{-- HERO SECTION --}}
    <section class="relative pt-10 pb-0 overflow-visible bg-white">
        <div class="max-w-[1440px] mx-auto px-4 md:px-12 relative z-10">
            <div class="bg-gradient-to-b from-[#3B4EFC] to-[#95D2FF] rounded-[34px] p-12 md:p-24 pb-32 md:pb-48 relative overflow-hidden min-h-[700px] flex flex-col items-center justify-center text-center">
                {{-- Background Chart Image --}}
                <div class="absolute inset-0 opacity-40 mix-blend-overlay pointer-events-none">
                    <img src="{{ asset('img/about/about-hero-bg.png') }}"
                         alt="Chart"
                         class="w-full h-full object-cover">
                </div>

                <h1 class="text-5xl md:text-7xl lg:text-[130px] font-the-bold-font font-black text-white leading-[0.9] mb-12 relative z-10 uppercase tracking-tighter">
                    DIGITAL ASSETS
                </h1>

                <div class="max-w-[939px] mx-auto relative z-10">
                    <p class="text-xl md:text-[28px] text-white leading-normal">
                        At <span class="font-bold text-[#E3FF3B]">Luma Stake</span>, we provide a secure, transparent
                        staking solution for those who value consistency, clarity, and long-term growth.
                    </p>
                </div>

                {{-- Yellow Arrow --}}
                <img src="{{ asset('img/about/hero_arrow.svg') }}"
                     alt=""
                     class="hero_arrow absolute bottom-20 left-1/2 h-auto z-20 pointer-events-none">
            </div>
        </div>
    </section>

    {{-- PROCESS / MISSION / VISION --}}
    <section class="pt-0 pb-12 bg-white relative">
        <div class="max-w-6xl mx-auto px-4 md:px-12 grid md:grid-cols-3 gap-8 -mt-24 md:-mt-32 relative z-30">
            {{-- Process --}}
            <div class="bg-white border border-[#2BA6FF] rounded-3xl p-6 md:p-7 shadow-[0_10px_30px_rgba(43,166,255,0.18)] transition-transform hover:scale-[1.02]">

                <!-- ICON + TITLE -->
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 md:w-14 md:h-14 flex-shrink-0">
                        <img src="{{ asset('img/about/icon-process.png') }}"
                             alt="Process"
                             class="w-full h-full object-contain">
                    </div>
                    <h3 class="text-2xl md:text-3xl font-black text-[#3B4EFC] leading-[1]">
                        Process
                    </h3>
                </div>

                <!-- TEXT -->
                <p class="text-[#262262] text-sm md:text-base leading-relaxed">
                    Luma Stake bridges complex blockchain technology with practical wealth generation—using advanced
                    staking and real-time automation to deliver stable, efficient earnings without speculation or
                    constant market monitoring.
                </p>

            </div>

            {{-- Mission --}}
            <div class="bg-white border border-[#2BA6FF] rounded-3xl p-6 md:p-7 shadow-[0_10px_30px_rgba(43,166,255,0.18)] transition-transform hover:scale-[1.02]">

                <!-- ICON + TITLE -->
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 md:w-14 md:h-14 flex-shrink-0">
                        <img src="{{ asset('img/about/icon-mission.png') }}"
                             alt="Mission"
                             class="w-full h-full object-contain">
                    </div>
                        <h3 class="text-2xl md:text-3xl font-black text-[#3B4EFC] leading-[1]">
                            Mission</h3>
                </div>
                    <p class="text-[#262262] text-sm md:text-base leading-relaxed">
                        To provide a secure and structured environment where users can stake with confidence, earn
                        passively, and grow their portfolios in a sustainable way.
                    </p>

            </div>
                {{-- Vision --}}
                <div class="bg-white border border-[#2BA6FF] rounded-3xl p-6 md:p-7 shadow-[0_10px_30px_rgba(43,166,255,0.18)] transition-transform hover:scale-[1.02]">

                    <!-- ICON + TITLE -->
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 md:w-14 md:h-14 flex-shrink-0">
                            <img src="{{ asset('img/about/icon-vision.png') }}"
                                 alt="Vision"
                                 class="w-full h-full object-contain">
                        </div>
                            <h3 class="text-2xl md:text-3xl font-black text-[#3B4EFC] leading-[1]">
                                Vision</h3>
                    </div>
                        <p class="text-[#262262] text-base md:text-[18px] leading-relaxed">
                            To become a global leader in decentralized wealth solutions by consistently delivering
                            results,
                            building trust, and prioritizing user experience.
                        </p>

                </div>
        </div>
    </section>

    {{-- WHY LUMA STAKE --}}

    <section class="py-24 bg-white relative">
        <img src="{{ asset('img/home/how-it-works-blue.svg') }}"
             alt=""
             class="absolute left-0 bottom-0 w-[620px] h-[620px] pointer-events-none select-none">

        <img src="{{ asset('img/home/how-it-works-yellow.svg') }}"
             alt=""
             class="absolute right-0 top-0 w-[1050px] h-[1050px] pointer-events-none select-none">
        <div class="max-w-[1440px] mx-auto px-4 md:px-12">
            <!-- Title (чуть меньше) -->
            <h2
                class="font-the-bold-font font-black uppercase tracking-tighter leading-[0.9]
             text-[#3B4EFC] mb-14
             text-[56px] md:text-[72px]"
            >
                WHY LUMA STAKE ?
            </h2>

            <!-- Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Row 1 / Left (wide) -->
                <div class="lg:col-span-8">
                    <div
                        class="bg-white/47 backdrop-blur-sm border border-[#2BA6FF]
                 p-10 rounded-[8px] min-h-[260px] flex flex-col justify-center"
                    >
                        <h3 class="font-bold text-[#262262] mb-6 leading-[0.93] text-[44px] md:text-[56px]">
                            Predictable Returns
                        </h3>
                        <p class="text-[22px] text-[#22253B]/79 leading-normal max-w-[52ch]">
                            Our staking system is designed for performance, not promises. Earnings are based on clear terms and transparent structures.
                        </p>
                    </div>
                </div>

                <!-- Row 1 / Right (narrow) -->
                <div class="lg:col-span-4">
                    <div
                        class="bg-white/47 backdrop-blur-sm border border-[#2BA6FF]
                 p-10 rounded-[8px] min-h-[260px] flex flex-col justify-center"
                    >
                        <h3 class="font-bold text-[#262262] mb-6 leading-[0.93] text-[44px] md:text-[56px]">
                            Capital Protection
                        </h3>
                        <p class="text-[22px] text-black/70 leading-normal">
                            Security is our foundation. We follow strict protocols and maintain a risk-managed approach to staking.
                        </p>
                    </div>
                </div>

                <!-- Row 2 / Centered -->
                <div class="lg:col-span-8 lg:col-start-3">
                    <div
                        class="bg-white/47 backdrop-blur-sm border border-[#2BA6FF]
                 p-10 rounded-[8px] min-h-[320px] flex flex-col justify-center"
                    >
                        <h3 class="font-bold text-[#262262] mb-6 leading-[0.93] text-[44px] md:text-[56px] text-center">
                            User Centric Design
                        </h3>
                        <p class="text-[22px] text-[#22253B]/79 leading-normal text-center mx-auto max-w-[68ch]">
                            The Luma stake interface is built to remove confusion. With easy onboarding, straightforward tiers, and live earnings tracking, users stay in control at every step.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- PNG blobs/шарики: оставь свои absolute <img> тут, если они у тебя есть -->
    </section>

    {{-- HOW WE GENERATE PROFIT --}}
    <section class="py-20 bg-white">
        <div class="max-w-[1440px] mx-auto px-4 md:px-12">
            <div class="bg-gradient-to-r from-[#3B4EFC] to-[#95D2FF] border border-[#2BA6FF] rounded-[18px] p-10 text-center mb-16 h-[141px] flex items-center justify-center">
                <h2 class="text-4xl md:text-[70px] font-the-bold-font font-black text-white uppercase leading-[0.97] tracking-tighter">
                    HOW WE GENERATE PROFIT ?
                </h2>
            </div>

            <div class="max-w-[936px] mx-auto text-center">
                <p class="text-2xl md:text-[28px] text-black/70 leading-normal">
                    We are a professional <span class="font-semibold text-[#22253B]/79">arbitrage firm.</span> Staked
                    funds are used in low-risk arbitrage trades that profit from price differences across platforms.
                    This method works in any market up or down — as it relies on
                    <span class="font-semibold text-black/70">volatility,</span> not direction. Profits are shared
                    between Luma Stake and our users, powered by a team of experts with
                    <span class="font-semibold text-black/70">10+ years of experience.</span>
                </p>
            </div>
        </div>
    </section>

    {{-- WHO WE SERVE --}}
    <section class="py-20 bg-white">
        <div class="max-w-[1440px] mx-auto px-4 md:px-12">
            <div class="bg-white/47 border border-[#2BA6FF] rounded-[8px] p-12 relative shadow-[0_4px_4px_0_rgba(0,0,0,0.25)] flex flex-col justify-center min-h-[311px]">
                <div class="absolute left-[-6px] top-[40px] bottom-[40px] w-[6px] bg-[#2BA6FF] rounded-[6px]"></div>
                <h2 class="text-4xl md:text-[40px] font-the-bold-font font-black text-[#3B4EFC] mb-8 uppercase leading-[0.9] ml-4 tracking-tighter">
                    Who we serve</h2>
                <p class="text-2xl md:text-[24px] text-[#22253B]/79 leading-normal ml-4">
                    Luma stake is trusted by working professionals, crypto enthusiasts, passive income seekers, and
                    anyone who wants their capital to work harder — without unnecessary risk.
                </p>
            </div>
        </div>
    </section>

@endsection
