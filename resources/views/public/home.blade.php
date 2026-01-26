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
                        <svg alt="Staking Periods" width="69" height="69" class="w-full h-auto" viewBox="0 0 69 69" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <mask id="mask0_9_640" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="69" height="69">
                                <rect width="68.0155" height="68.0155" fill="url(#pattern0_9_640)"/>
                            </mask>
                            <g mask="url(#mask0_9_640)">
                                <rect x="-14.6426" y="-17.9485" width="108.636" height="113.359" fill="#262262"/>
                            </g>
                            <defs>
                                <pattern id="pattern0_9_640" patternContentUnits="objectBoundingBox" width="1" height="1">
                                    <use xlink:href="#image0_9_640" transform="scale(0.00195312)"/>
                                </pattern>
                                <image id="image0_9_640" width="512" height="512" preserveAspectRatio="none" xlink:href="data:image/png;base64,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"/>
                            </defs>
                        </svg>
                    </div>
                    <div class="text-lg md:text-xl text-lumastake-navy font-medium leading-tight">
                        Staking Periods from <br> <span class="font-bold">10 to 180 Days</span>
                    </div>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-24 h-24 mb-6 flex items-center justify-center">
                        <svg width="63" height="63" viewBox="0 0 63 63" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <mask id="mask0_9_644" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="63" height="63">
                                <rect width="62.7835" height="62.7835" fill="url(#pattern0_9_644)"/>
                            </mask>
                            <g mask="url(#mask0_9_644)">
                                <rect x="-8.71191" y="-8.41113" width="100.634" height="93.1239" fill="#262262"/>
                            </g>
                            <defs>
                                <pattern id="pattern0_9_644" patternContentUnits="objectBoundingBox" width="1" height="1">
                                    <use xlink:href="#image0_9_644" transform="scale(0.00195312)"/>
                                </pattern>
                                <image id="image0_9_644" width="512" height="512" preserveAspectRatio="none" xlink:href="data:image/png;base64,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"/>
                            </defs>
                        </svg>
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
