@extends('layouts.public')

@section('content')
    {{-- HERO SECTION --}}
    <section class="relative pt-10 pb-0 overflow-visible bg-white">
        <div class="max-w-[1440px] mx-auto px-4 md:px-12 relative z-10">
            <div class="bg-gradient-to-b from-[#3B4EFC] to-[#95D2FF] rounded-[34px] p-12 md:p-24 pb-32 md:pb-48 relative overflow-hidden min-h-[700px] flex flex-col items-center justify-center text-center">
                {{-- Background Chart Image --}}
                <div class="absolute inset-0 opacity-40 mix-blend-overlay pointer-events-none">
                    <img src="{{ asset('img/about/about-hero-bg.png') }}" alt="Chart" class="w-full h-full object-cover">
                </div>

                <h1 class="text-5xl md:text-7xl lg:text-[130px] font-the-bold-font font-black text-white leading-[0.9] mb-12 relative z-10 uppercase tracking-tighter">
                    DIGITAL ASSETS
                </h1>

                <div class="max-w-[939px] mx-auto relative z-10">
                    <p class="text-xl md:text-[28px] text-white leading-normal">
                        At <span class="font-bold text-[#E3FF3B]">Luma Stake</span>, we provide a secure, transparent staking solution for those who value consistency, clarity, and long-term growth.
                    </p>
                </div>

                {{-- Yellow Arrow --}}
                <img src="{{ asset('img/about/hero_arrow.svg') }}" alt="" class="absolute bottom-20 md:bottom-32 left-1/2 -translate-x-1/2 w-[300px] md:w-[500px] lg:w-[700px] h-auto z-20 pointer-events-none">
            </div>
        </div>
    </section>

    {{-- PROCESS / MISSION / VISION --}}
    <section class="pt-0 pb-12 bg-white relative">
        <div class="max-w-[1440px] mx-auto px-4 md:px-12 grid md:grid-cols-3 gap-8 -mt-24 md:-mt-32 relative z-30">
            {{-- Process --}}
            <div class="bg-white border border-[#2BA6FF] rounded-[30px] p-8 md:p-10 shadow-[0_4px_20px_0_rgba(43,166,255,0.25)] flex flex-col items-center text-center transition-transform hover:scale-[1.02]">
                <div class="w-[70px] h-[70px] mb-6">
                    <img src="{{ asset('img/about/icon-process.png') }}" alt="Process" class="w-full h-full object-contain">
                </div>
                <h3 class="text-[32px] md:text-[40px] font-black text-[#3B4EFC] mb-4 capitalize leading-[1]">Process</h3>
                <p class="text-[#262262] text-base md:text-[18px] leading-relaxed">
                    Luma Stake bridges complex blockchain technology with practical wealth generation—using advanced staking and real-time automation to deliver stable, efficient earnings without speculation or constant market monitoring.
                </p>
            </div>

            {{-- Mission --}}
            <div class="bg-white border border-[#2BA6FF] rounded-[30px] p-8 md:p-10 shadow-[0_4px_20px_0_rgba(43,166,255,0.25)] flex flex-col items-center text-center transition-transform hover:scale-[1.02]">
                <div class="w-[70px] h-[70px] mb-6">
                    <img src="{{ asset('img/about/icon-mission.png') }}" alt="Mission" class="w-full h-full object-contain">
                </div>
                <h3 class="text-[32px] md:text-[40px] font-black text-[#3B4EFC] mb-4 capitalize leading-[1]">Mission</h3>
                <p class="text-[#262262] text-base md:text-[18px] leading-relaxed">
                    To provide a secure and structured environment where users can stake with confidence, earn passively, and grow their portfolios in a sustainable way.
                </p>
            </div>

            {{-- Vision --}}
            <div class="bg-white border border-[#2BA6FF] rounded-[30px] p-8 md:p-10 shadow-[0_4px_20px_0_rgba(43,166,255,0.25)] flex flex-col items-center text-center transition-transform hover:scale-[1.02]">
                <div class="w-[70px] h-[70px] mb-6">
                    <img src="{{ asset('img/about/icon-vision.png') }}" alt="Vision" class="w-full h-full object-contain">
                </div>
                <h3 class="text-[32px] md:text-[40px] font-black text-[#3B4EFC] mb-4 capitalize leading-[1]">Vision</h3>
                <p class="text-[#262262] text-base md:text-[18px] leading-relaxed">
                    To become a global leader in decentralized wealth solutions by consistently delivering results, building trust, and prioritizing user experience.
                </p>
            </div>
        </div>
    </section>

    {{-- WHY LUMA STAKE --}}
    <section class="py-24 bg-white overflow-hidden relative">
        <div class="max-w-[1440px] mx-auto px-4 md:px-12">
            <h2 class="text-5xl md:text-[80px] font-the-bold-font font-black text-[#3B4EFC] mb-16 uppercase leading-[0.9] tracking-tighter">
                WHY LUMA STAKE ?
            </h2>

            <div class="grid lg:grid-cols-12 gap-8 items-stretch">
                <div class="lg:col-span-7 space-y-8">
                    {{-- Predictable Returns --}}
                    <div class="bg-white/47 backdrop-blur-sm border border-[#2BA6FF] p-10 rounded-[8px] relative min-h-[267px] flex flex-col justify-center">
                        <h3 class="text-4xl md:text-[50px] font-bold text-[#262262] mb-6 leading-[0.93]">Predictable Returns</h3>
                        <p class="text-[22px] text-[#22253B]/79 leading-normal">
                            Our staking system is designed for performance, not promises. Earnings are based on clear terms and transparent structures.
                        </p>
                    </div>

                    {{-- User Centric Design --}}
                    <div class="bg-white/47 backdrop-blur-sm border border-[#2BA6FF] p-10 rounded-[8px] min-h-[334px] flex flex-col justify-center">
                        <h3 class="text-4xl md:text-[50px] font-bold text-[#262262] mb-6 leading-[0.93]">User Centric Design</h3>
                        <p class="text-[22px] text-[#22253B]/79 leading-normal">
                            The Luma stake interface is built to remove confusion. With easy onboarding, straightforward tiers, and live earnings tracking, users stay in control at every step.
                        </p>
                    </div>
                </div>

                {{-- Capital Protection --}}
                <div class="lg:col-span-5 bg-white/47 backdrop-blur-sm border border-[#2BA6FF] p-10 rounded-[8px] flex flex-col justify-center min-h-[371px]">
                    <h3 class="text-4xl md:text-[50px] font-bold text-[#262262] mb-6 leading-[0.93]">Capital Protection</h3>
                    <p class="text-[22px] text-black/70 leading-normal">
                        Security is our foundation. We follow strict protocols and maintain a risk-managed approach to staking.
                    </p>
                </div>
            </div>
        </div>

        {{-- Decorative Images --}}
        <div class="absolute right-[-100px] top-[10%] w-[500px] h-[500px] bg-[#3B4EFC]/5 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute left-[-100px] bottom-[10%] w-[400px] h-[400px] bg-[#3B4EFC]/5 rounded-full blur-[80px] pointer-events-none"></div>
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
                    We are a professional <span class="font-semibold text-[#22253B]/79">arbitrage firm.</span> Staked funds are used in low-risk arbitrage trades that profit from price differences across platforms. This method works in any market up or down — as it relies on <span class="font-semibold text-black/70">volatility,</span> not direction. Profits are shared between Luma Stake and our users, powered by a team of experts with <span class="font-semibold text-black/70">10+ years of experience.</span>
                </p>
            </div>
        </div>
    </section>

    {{-- WHO WE SERVE --}}
    <section class="py-20 bg-white">
        <div class="max-w-[1440px] mx-auto px-4 md:px-12">
            <div class="bg-white/47 border border-[#2BA6FF] rounded-[8px] p-12 relative shadow-[0_4px_4px_0_rgba(0,0,0,0.25)] flex flex-col justify-center min-h-[311px]">
                <div class="absolute left-0 top-[40px] bottom-[40px] w-[6px] bg-[#2BA6FF] rounded-[6px]"></div>
                <h2 class="text-4xl md:text-[40px] font-the-bold-font font-black text-[#3B4EFC] mb-8 uppercase leading-[0.9] ml-4 tracking-tighter">Who we serve</h2>
                <p class="text-2xl md:text-[24px] text-[#22253B]/79 leading-normal ml-4">
                    Luma stake is trusted by working professionals, crypto enthusiasts, passive income seekers, and anyone who wants their capital to work harder — without unnecessary risk.
                </p>
            </div>
        </div>
    </section>

@endsection
