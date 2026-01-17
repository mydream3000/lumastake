@extends('layouts.public')

@section('content')
    {{-- 1. HERO SECTION --}}
    <section class="relative min-h-[900px] flex items-center justify-center overflow-hidden bg-white pt-20">
        {{-- Background Elements --}}
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('img/home/hero-trade.png') }}" alt="" class="absolute top-[166px] left-1/2 -translate-x-1/2 max-w-none w-[1589px] h-auto opacity-80">
            <img src="{{ asset('img/home/hero-design.png') }}" alt="" class="absolute top-[608px] left-0 w-full h-auto object-cover">
        </div>

        <div class="max-w-7xl mx-auto px-4 relative z-10 text-center -mt-20">
            <h1 class="text-4xl md:text-[52px] font-black text-lumastake-blue mb-8 leading-[0.9] uppercase tracking-tight">
                The Smarter Way to Grow Your USDT
            </h1>
            <p class="text-xl md:text-[28px] text-lumastake-navy mb-6 max-w-4xl mx-auto leading-normal">
                LumaStake removes the noise of trading and offers a clean, secure path to passive rewards.
            </p>
            <p class="text-xl md:text-[28px] text-lumastake-navy mb-12 max-w-4xl mx-auto leading-normal">
                Set up your wallet, pick your plan, and watch your staking power grow.
            </p>

            <div class="flex justify-center">
                <a href="{{ route('register') }}" class="bg-lumastake-lime text-lumastake-navy px-12 py-5 rounded shadow-lg text-2xl font-bold hover:bg-[#c4e600] transition-all transform hover:scale-105 uppercase">
                    Start Staking
                </a>
            </div>
        </div>
    </section>

    {{-- 2. WHY CHOOSE SECTION --}}
    <section class="py-32 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 md:px-6">
            <div class="bg-lumastake-light-blue border border-[#2BA6FF] rounded-xl p-10 md:p-20 relative overflow-hidden">
                <div class="grid lg:grid-cols-2 gap-12 items-center relative z-10">
                    <div>
                        <h2 class="text-6xl md:text-[100px] font-black text-lumastake-blue leading-[0.9] uppercase mb-10">
                            WHY CHOOSE <br> LUMA STAKE
                        </h2>
                        <p class="text-xl md:text-2xl text-black leading-relaxed max-w-xl">
                            Luma stake is designed for clarity, safety, and real returns. No complex charts. No price speculation. Just stablecoin staking that delivers.
                        </p>
                    </div>
                    <div>
                        <ul class="space-y-8">
                            <li class="flex items-start text-2xl font-medium text-black">
                                <span class="w-2 h-2 rounded-full bg-black mt-3 mr-4 flex-shrink-0"></span>
                                100% staking-based income
                            </li>
                            <li class="flex items-start text-2xl font-medium text-black">
                                <span class="w-2 h-2 rounded-full bg-black mt-3 mr-4 flex-shrink-0"></span>
                                Zero exposure to trading or leverage
                            </li>
                            <li class="flex items-start text-2xl font-medium text-black">
                                <span class="w-2 h-2 rounded-full bg-black mt-3 mr-4 flex-shrink-0"></span>
                                Transparent returns — always visible
                            </li>
                            <li class="flex items-start text-2xl font-medium text-black">
                                <span class="w-2 h-2 rounded-full bg-black mt-3 mr-4 flex-shrink-0"></span>
                                Built for everyday users, not just tech experts
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. FEATURES SECTION --}}
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h2 class="text-5xl md:text-[60px] font-black text-lumastake-navy uppercase mb-6">features</h2>
            <p class="text-2xl md:text-[32px] text-lumastake-dark mb-24">Everything you need to grow your crypto, nothing you don’t.</p>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-12">
                <div class="flex flex-col items-center">
                    <div class="w-24 h-24 mb-6 flex items-center justify-center">
                        <img src="{{ asset('img/home/feature-1.png') }}" alt="Staking Periods" class="w-full h-auto">
                    </div>
                    <div class="text-lg md:text-xl text-lumastake-navy font-medium leading-tight">
                        Staking Periods from <br> <span class="font-bold">10 to 180 Days</span>
                    </div>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-24 h-24 mb-6 flex items-center justify-center">
                        <img src="{{ asset('img/home/feature-2.png') }}" alt="Live Dashboard" class="w-full h-auto">
                    </div>
                    <div class="text-lg md:text-xl text-lumastake-navy font-medium leading-tight">
                        Live Dashboard to <br> <span class="font-bold">Track Your Growth</span>
                    </div>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-24 h-24 mb-6 flex items-center justify-center">
                        <img src="{{ asset('img/home/feature-3.png') }}" alt="Deposits & Withdrawals" class="w-full h-auto">
                    </div>
                    <div class="text-lg md:text-xl text-lumastake-navy font-medium leading-tight">
                        Quick USDT Deposits & <br> <span class="font-bold">Withdrawals</span>
                    </div>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-24 h-24 mb-6 flex items-center justify-center">
                        <img src="{{ asset('img/home/feature-4.png') }}" alt="Multi-Tier" class="w-full h-auto">
                    </div>
                    <div class="text-lg md:text-xl text-lumastake-navy font-medium leading-tight">
                        Multi-Tier <br> <span class="font-bold">Earning Model</span>
                    </div>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-24 h-24 mb-6 flex items-center justify-center">
                        <img src="{{ asset('img/home/feature-5.png') }}" alt="Mobile & Desktop" class="w-full h-auto">
                    </div>
                    <div class="text-lg md:text-xl text-lumastake-navy font-medium leading-tight">
                        Works Seamlessly on <br> <span class="font-bold">Mobile & Desktop</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 4. STEPS SECTION (How it works) --}}
    <section class="py-32 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-20 items-center">
                <div class="lg:w-1/2">
                    <h2 class="text-6xl md:text-[100px] font-black text-lumastake-blue leading-[0.9] uppercase mb-10">HOW IT WORKS</h2>
                    <p class="text-xl md:text-2xl text-black leading-relaxed">
                        Get started in minutes with a simple, intuitive setup—no delays, no complicated onboarding. Jump straight into sign-up to action and start seeing value right away.
                    </p>
                </div>
                <div class="lg:w-1/2 space-y-10 relative">
                    {{-- Step 1 --}}
                    <div class="bg-white border border-[#2BA6FF] rounded-2xl p-10 flex items-center relative z-10">
                        <div class="text-[200px] font-audiowide text-[#E0F2FF] absolute left-[-40px] top-1/2 -translate-y-1/2 opacity-50 z-0 select-none">1</div>
                        <div class="relative z-10 pl-20">
                            <h3 class="text-4xl md:text-[52px] font-black text-lumastake-navy mb-2 uppercase">SIGN UP</h3>
                            <p class="text-xl text-gray-600">Create your account. It's fast, simple, and free.</p>
                        </div>
                    </div>
                    {{-- Step 2 --}}
                    <div class="bg-lumastake-blue rounded-2xl p-10 flex items-center shadow-2xl relative z-20">
                        <div class="text-[200px] font-audiowide text-white/10 absolute left-[-40px] top-1/2 -translate-y-1/2 select-none">2</div>
                        <div class="relative z-10 pl-20">
                            <h3 class="text-3xl md:text-[40px] font-black text-white mb-2 uppercase">Choose a Staking Platform</h3>
                            <p class="text-xl text-white/80">Pick your duration, your USDT starts working immediately.</p>
                        </div>
                    </div>
                    {{-- Step 3 --}}
                    <div class="bg-white border border-[#2BA6FF] rounded-2xl p-10 flex items-center relative z-10">
                        <div class="text-[200px] font-audiowide text-[#E0F2FF] absolute left-[-40px] top-1/2 -translate-y-1/2 opacity-50 z-0 select-none">3</div>
                        <div class="relative z-10 pl-20">
                            <h3 class="text-4xl md:text-[52px] font-black text-lumastake-navy mb-2 uppercase">Deposit USDT</h3>
                            <p class="text-xl text-gray-600">Add funds quickly and securely.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 5. BENEFITS SECTION --}}
    <section class="py-32 bg-white">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h2 class="text-6xl md:text-[100px] font-black text-lumastake-blue uppercase mb-10">BENEFITS</h2>
            <p class="text-2xl md:text-[32px] text-gray-600 mb-24">Why people are switching to Lumastake</p>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-x-12 gap-y-20">
                <div class="flex flex-col items-center">
                    <div class="w-32 h-32 mb-8 bg-lumastake-blue rounded-full flex items-center justify-center p-8">
                        <img src="{{ asset('img/home/icon-fixed-returns.png') }}" alt="" class="w-full h-auto">
                    </div>
                    <p class="text-xl md:text-[28px] text-black font-medium leading-tight">Fixed returns, no price swings</p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-32 h-32 mb-8 bg-lumastake-blue rounded-full flex items-center justify-center p-8">
                        <img src="{{ asset('img/home/icon-tier-based.png') }}" alt="" class="w-full h-auto">
                    </div>
                    <p class="text-xl md:text-[28px] text-black font-medium leading-tight">Tier-based rewards system</p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-32 h-32 mb-8 bg-lumastake-blue rounded-full flex items-center justify-center p-8">
                        <img src="{{ asset('img/home/icon-clean.png') }}" alt="" class="w-full h-auto">
                    </div>
                    <p class="text-xl md:text-[28px] text-black font-medium leading-tight">Clean, clutter-free experience</p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-32 h-32 mb-8 bg-lumastake-blue rounded-full flex items-center justify-center p-8">
                        <img src="{{ asset('img/home/icon-hands-off.png') }}" alt="" class="w-full h-auto">
                    </div>
                    <p class="text-xl md:text-[28px] text-black font-medium leading-tight">Hands-off experience—no trading needed</p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-32 h-32 mb-8 bg-lumastake-blue rounded-full flex items-center justify-center p-8">
                        <img src="{{ asset('img/home/icon-mobile.png') }}" alt="" class="w-full h-auto">
                    </div>
                    <p class="text-xl md:text-[28px] text-black font-medium leading-tight">Mobile-friendly interface</p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-32 h-32 mb-8 bg-lumastake-blue rounded-full flex items-center justify-center p-8">
                        <img src="{{ asset('img/home/icon-transparent.png') }}" alt="" class="w-full h-auto">
                    </div>
                    <p class="text-xl md:text-[28px] text-black font-medium leading-tight">Transparent from day one</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 6. CTA MIDDLE --}}
    <section class="py-32 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="bg-lumastake-light-blue rounded-[3rem] p-12 lg:p-24 flex flex-col lg:flex-row items-center relative overflow-hidden">
                <div class="lg:w-1/2 space-y-10 relative z-10">
                    <p class="text-xl md:text-2xl font-bold text-lumastake-blue uppercase tracking-[0.2em]">Turning crypto complexity into opportunity</p>
                    <h2 class="text-5xl md:text-[80px] font-black text-lumastake-navy leading-[0.9]">LESS CONFUSION. <br> MORE CONTROL.</h2>
                    <p class="text-xl md:text-2xl text-gray-700 max-w-lg leading-relaxed">
                        We built Lumastake to remove the noise from crypto. No tokens to flip, no charts to trade. Just one goal: make your stablecoins work more, safely and smoothly.
                    </p>
                    <a href="{{ route('register') }}" class="inline-block bg-lumastake-lime text-lumastake-navy px-12 py-5 rounded shadow-lg text-2xl font-bold hover:bg-[#c4e600] transition-all transform hover:scale-105 uppercase">
                        Start Staking
                    </a>
                </div>
                <div class="lg:w-1/2 mt-16 lg:mt-0 relative">
                    <img src="{{ asset('img/home/pyramid.png') }}" alt="Lumastake Ecosystem" class="w-full h-auto transform scale-110">
                </div>
            </div>
        </div>
    </section>

    {{-- 7. SECURE YOUR FUTURE --}}
    <section class="py-32 bg-white">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h2 class="text-6xl md:text-[100px] font-black text-lumastake-blue uppercase mb-10 leading-[0.9]">SECURE YOUR FUTURE</h2>
            <p class="text-2xl md:text-[32px] text-gray-600 mb-24">Your money stays protected. Always.</p>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="p-10 rounded-2xl bg-[#FFFFF0] text-left border-l-[12px] border-lumastake-blue shadow-sm">
                    <p class="text-2xl font-medium text-black">Strong encryption protects your wallet</p>
                </div>
                <div class="p-10 rounded-2xl bg-[#F0F9FF] text-left border-l-[12px] border-lumastake-blue shadow-sm">
                    <p class="text-2xl font-medium text-black">Two-factor authentication (2FA)</p>
                </div>
                <div class="p-10 rounded-2xl bg-[#FDF2F2] text-left border-l-[12px] border-lumastake-blue shadow-sm">
                    <p class="text-2xl font-medium text-black">All deposits and withdrawals in USDT</p>
                </div>
                <div class="p-10 rounded-2xl bg-[#F0FDF4] text-left border-l-[12px] border-lumastake-blue shadow-sm">
                    <p class="text-2xl font-medium text-black">Cold wallet storage for most funds</p>
                </div>
                <div class="p-10 rounded-2xl bg-[#F5F3FF] text-left border-l-[12px] border-lumastake-blue shadow-sm">
                    <p class="text-2xl font-medium text-black">Regular system checks and audits</p>
                </div>
                <div class="p-10 rounded-2xl bg-[#FFF7ED] text-left border-l-[12px] border-lumastake-blue shadow-sm">
                    <p class="text-2xl font-medium text-black">Instant withdrawals, just minutes away</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 8. NEWS & INSIGHTS --}}
    <section class="py-32 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="bg-lumastake-light-blue rounded-[3rem] p-12 lg:p-24 relative overflow-hidden min-h-[600px] flex items-center">
                <div class="relative z-10 w-full lg:w-2/3">
                    <h2 class="text-6xl md:text-[100px] font-black text-lumastake-navy uppercase mb-10 leading-[0.9]">News & Insights</h2>
                    <p class="text-2xl md:text-[32px] text-gray-600 mb-20 leading-tight">Stay ahead with the latest in staking and stablecoins.</p>

                    <div>
                        <h3 class="text-5xl md:text-[60px] font-black text-lumastake-blue uppercase mb-6 leading-none">COMING SOON</h3>
                        <p class="text-xl md:text-2xl text-gray-600 max-w-xl leading-relaxed">
                            Lumastake Academy — tips, guides and expert interviews to help you make smarter crypto moves.
                        </p>
                    </div>
                </div>
                {{-- Decorative Circle --}}
                <div class="absolute right-[-100px] bottom-[-100px] w-[600px] h-[600px] rounded-full border-[80px] border-white opacity-40 z-0"></div>
            </div>
        </div>
    </section>

    {{-- 9. TEAM --}}
    <section class="py-32 bg-white">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="grid md:grid-cols-3 gap-16">
                <div class="space-y-8">
                    <div class="relative inline-block">
                        <img src="{{ asset('img/home/team-1.png') }}" alt="Alan Campbell" class="w-80 h-80 object-cover rounded-[3rem] shadow-xl">
                    </div>
                    <div>
                        <h3 class="text-4xl font-black text-lumastake-navy">Alan Campbell</h3>
                        <p class="text-2xl font-bold text-lumastake-blue">CEO</p>
                    </div>
                </div>
                <div class="space-y-8">
                    <div class="relative inline-block">
                        <img src="{{ asset('img/home/team-2.png') }}" alt="Emily Rossi" class="w-80 h-80 object-cover rounded-[3rem] shadow-xl">
                    </div>
                    <div>
                        <h3 class="text-4xl font-black text-lumastake-navy">Emily Rossi</h3>
                        <p class="text-2xl font-bold text-lumastake-blue">CFO</p>
                    </div>
                </div>
                <div class="space-y-8">
                    <div class="relative inline-block">
                        <img src="{{ asset('img/home/team-3.png') }}" alt="Christopher Taylor" class="w-80 h-80 object-cover rounded-[3rem] shadow-xl">
                    </div>
                    <div>
                        <h3 class="text-4xl font-black text-lumastake-navy">Christopher Taylor</h3>
                        <p class="text-2xl font-bold text-lumastake-blue">COO</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
