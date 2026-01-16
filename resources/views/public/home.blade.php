@extends('layouts.public')

@section('content')
    {{-- HERO SECTION --}}
    <section class="relative pt-20 pb-32 overflow-hidden bg-white">
        <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-[600px] h-[600px] bg-blue-50 rounded-full blur-3xl opacity-50"></div>
        <div class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/4 w-[800px] h-[800px] bg-blue-50 rounded-full blur-3xl opacity-50"></div>

        <div class="max-w-7xl mx-auto px-4 relative z-10 text-center">
            <h1 class="text-4xl md:text-6xl font-extrabold text-[#3B4EFC] mb-6 leading-tight">
                The Smarter Way to Grow Your USDT
            </h1>
            <p class="text-xl md:text-2xl text-[#262262] mb-4 max-w-3xl mx-auto">
                LumaStake removes the noise of trading and offers a clean, secure path to passive rewards.
            </p>
            <p class="text-lg md:text-xl text-[#262262] mb-10 max-w-3xl mx-auto">
                Set up your wallet, pick your plan, and watch your staking power grow.
            </p>

            <div class="flex justify-center mb-16">
                <a href="{{ route('register') }}" class="bg-[#D9FF00] text-[#262262] px-10 py-4 rounded font-bold text-xl hover:bg-[#c4e600] transition-all transform hover:scale-105">
                    Start Staking
                </a>
            </div>

            <div class="relative max-w-4xl mx-auto">
                <img src="{{ asset('img/hero-money.png') }}" alt="Staking Growth" class="w-full h-auto">
            </div>
        </div>
    </section>

    {{-- WHY CHOOSE SECTION --}}
    <section class="py-20 bg-[#E0F2FF]">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-8">
                    <h2 class="text-5xl md:text-7xl font-black text-[#3B4EFC] leading-none uppercase">
                        WHY CHOOSE <br> LUMA STAKE
                    </h2>
                    <p class="text-xl text-gray-800 leading-relaxed">
                        Luma stake is designed for clarity, safety, and real returns. No complex charts. No price speculation. Just stablecoin staking that delivers.
                    </p>
                </div>
                <div class="bg-white/50 backdrop-blur p-8 rounded-2xl border border-blue-200">
                    <ul class="space-y-6">
                        <li class="flex items-start">
                            <span class="text-[#3B4EFC] mr-4 text-2xl">вЂў</span>
                            <span class="text-xl font-medium text-[#262262]">100% staking-based income</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-[#3B4EFC] mr-4 text-2xl">вЂў</span>
                            <span class="text-xl font-medium text-[#262262]">Zero exposure to trading or leverage</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-[#3B4EFC] mr-4 text-2xl">вЂў</span>
                            <span class="text-xl font-medium text-[#262262]">Transparent returns вЂ” always visible</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-[#3B4EFC] mr-4 text-2xl">вЂў</span>
                            <span class="text-xl font-medium text-[#262262]">Built for everyday users, not just tech experts</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- FEATURES SECTION --}}
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h2 class="text-5xl font-black text-[#262262] uppercase mb-4">features</h2>
            <p class="text-2xl text-gray-600 mb-16">Everything you need to grow your crypto, nothing you donвЂ™t.</p>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-8">
                <div class="flex flex-col items-center">
                    <div class="w-20 h-20 mb-4 bg-gray-50 rounded-2xl flex items-center justify-center">
                        <img src="{{ asset('img/912b6ed8dc17e254e9ea3c36a85e69e464d32f6a.png') }}" alt="Staking" class="w-12 h-12">
                    </div>
                    <p class="font-medium text-[#262262]">Staking Periods from <br> <span class="font-bold">10 to 180 Days</span></p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-20 h-20 mb-4 bg-gray-50 rounded-2xl flex items-center justify-center">
                        <img src="{{ asset('img/b02b8511fed31343874913c2df879411dea4de29.png') }}" alt="Dashboard" class="w-12 h-12">
                    </div>
                    <p class="font-medium text-[#262262]">Live Dashboard to <br> <span class="font-bold">Track Your Growth</span></p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-20 h-20 mb-4 bg-gray-50 rounded-2xl flex items-center justify-center">
                        <img src="{{ asset('img/8a777e4faaf9dc40e9939b65b4e231e4099637b2.png') }}" alt="Withdrawals" class="w-12 h-12">
                    </div>
                    <p class="font-medium text-[#262262]">Quick USDT Deposits <br> & <span class="font-bold">Withdrawals</span></p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-20 h-20 mb-4 bg-gray-50 rounded-2xl flex items-center justify-center">
                        <img src="{{ asset('img/e409fcd88907a72fd631e1db16736f36066eab1e.png') }}" alt="Multi-Tier" class="w-12 h-12">
                    </div>
                    <p class="font-medium text-[#262262]">Multi-Tier <br> <span class="font-bold">Earning Model</span></p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-20 h-20 mb-4 bg-gray-50 rounded-2xl flex items-center justify-center">
                        <img src="{{ asset('img/be938e57d5ee8fe6c15093b08c0f231db9b52c6c.png') }}" alt="Mobile" class="w-12 h-12">
                    </div>
                    <p class="font-medium text-[#262262]">Works Seamlessly on <br> <span class="font-bold">Mobile & Desktop</span></p>
                </div>
            </div>
        </div>
    </section>

    {{-- HOW IT WORKS --}}
    <section class="py-24 bg-blue-50/30">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="text-6xl font-black text-[#3B4EFC] uppercase mb-8">HOW IT WORKS</h2>
                    <p class="text-xl text-gray-700 mb-8">
                        Get started in minutes with a simple, intuitive setupвЂ”no delays, no complicated onboarding. Jump straight into sign-up to action and start seeing value right away.
                    </p>
                </div>
                <div class="space-y-6">
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-blue-100 flex items-center">
                        <div class="text-6xl font-black text-blue-100 mr-8">1</div>
                        <div>
                            <h3 class="text-3xl font-bold text-[#262262] mb-2 uppercase">SIGN UP</h3>
                            <p class="text-gray-600">Create your account. ItвЂ™s fast, simple, and free.</p>
                        </div>
                    </div>
                    <div class="bg-blue-600 p-8 rounded-2xl shadow-lg text-white flex items-center">
                        <div class="text-6xl font-black text-blue-400 mr-8 opacity-50">2</div>
                        <div>
                            <h3 class="text-3xl font-bold mb-2 uppercase">Choose a Staking Platform</h3>
                            <p class="text-blue-100">Pick your duration, your USDT starts working immediately.</p>
                        </div>
                    </div>
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-blue-100 flex items-center">
                        <div class="text-6xl font-black text-blue-100 mr-8">3</div>
                        <div>
                            <h3 class="text-3xl font-bold text-[#262262] mb-2 uppercase">Deposit USDT</h3>
                            <p class="text-gray-600">Add funds quickly and securely.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- BENEFITS --}}
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h2 class="text-6xl font-black text-[#3B4EFC] uppercase mb-4">BENEFITS</h2>
            <p class="text-2xl text-gray-600 mb-20">Why people are switching to Arbitex</p>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-12">
                <div class="flex flex-col items-center text-center">
                    <div class="w-20 h-20 mb-6 bg-blue-600 rounded-full flex items-center justify-center">
                        <img src="{{ asset('img/f5db2e8213dae77711adde1ebe72e3ce4259bf89.png') }}" alt="" class="w-10 h-10">
                    </div>
                    <p class="text-xl text-gray-800">Fixed returns, no price swings</p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="w-20 h-20 mb-6 bg-blue-600 rounded-full flex items-center justify-center">
                        <img src="{{ asset('img/47ae261b183fe2590bb649fe03ac303b5be7314c.png') }}" alt="" class="w-10 h-10">
                    </div>
                    <p class="text-xl text-gray-800">Tier-based rewards system</p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="w-20 h-20 mb-6 bg-blue-600 rounded-full flex items-center justify-center">
                        <img src="{{ asset('img/0639f2077c124b645cffccdcacca20c5a0992967.png') }}" alt="" class="w-10 h-10">
                    </div>
                    <p class="text-xl text-gray-800">Clean, clutter-free experience</p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="w-20 h-20 mb-6 bg-blue-600 rounded-full flex items-center justify-center">
                        <img src="{{ asset('img/3fe77745dace33e86b14ff8de78ce2be0c4b5632.png') }}" alt="" class="w-10 h-10">
                    </div>
                    <p class="text-xl text-gray-800">Hands-off experienceвЂ”no trading needed</p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="w-20 h-20 mb-6 bg-blue-600 rounded-full flex items-center justify-center">
                        <img src="{{ asset('img/7727dd6b6d9fc29c1b04d388929811436f8a2ddf.png') }}" alt="" class="w-10 h-10">
                    </div>
                    <p class="text-xl text-gray-800">Mobile-friendly interface</p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="w-20 h-20 mb-6 bg-blue-600 rounded-full flex items-center justify-center">
                        <img src="{{ asset('img/242ac05f40b1434373bb9c510f2749adf03a03b6.png') }}" alt="" class="w-10 h-10">
                    </div>
                    <p class="text-xl text-gray-800">Transparent from day one</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA MIDDLE --}}
    <section class="py-24 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4">
            <div class="bg-blue-50 rounded-[3rem] p-12 lg:p-20 flex flex-col lg:flex-row items-center relative">
                <div class="lg:w-1/2 space-y-8 relative z-10">
                    <p class="text-xl font-bold text-blue-600 uppercase tracking-widest">Turning crypto complexity into opportunity</p>
                    <h2 class="text-5xl md:text-6xl font-black text-[#262262] leading-tight">LESS CONFUSION. MORE CONTROL.</h2>
                    <p class="text-xl text-gray-600 max-w-lg">
                        We built Arbitex to remove the noise from crypto. No tokens to flip, no charts to trade. Just one goal: make your stablecoins work more, safely and smoothly.
                    </p>
                    <a href="{{ route('register') }}" class="inline-block bg-[#D9FF00] text-[#262262] px-10 py-4 rounded font-bold text-xl hover:bg-[#c4e600] transition-all">
                        Start Staking
                    </a>
                </div>
                <div class="lg:w-1/2 mt-12 lg:mt-0 relative">
                    <img src="{{ asset('img/pyramid.png') }}" alt="Arbitex Ecosystem" class="w-full h-auto">
                </div>
            </div>
        </div>
    </section>

    {{-- SECURE YOUR FUTURE --}}
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h2 class="text-6xl font-black text-[#3B4EFC] uppercase mb-4">SECURE YOUR FUTURE</h2>
            <p class="text-2xl text-gray-600 mb-20">Your money stays protected. Always.</p>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="p-8 rounded-2xl bg-[#FFFFF0] text-left border-l-4 border-blue-500">
                    <p class="text-xl text-gray-800">Strong encryption protects your wallet</p>
                </div>
                <div class="p-8 rounded-2xl bg-[#F0F9FF] text-left border-l-4 border-blue-500">
                    <p class="text-xl text-gray-800">Two-factor authentication (2FA)</p>
                </div>
                <div class="p-8 rounded-2xl bg-[#FDF2F2] text-left border-l-4 border-blue-500">
                    <p class="text-xl text-gray-800">All deposits and withdrawals in USDT</p>
                </div>
                <div class="p-8 rounded-2xl bg-[#F0FDF4] text-left border-l-4 border-blue-500">
                    <p class="text-xl text-gray-800">Cold wallet storage for most funds</p>
                </div>
                <div class="p-8 rounded-2xl bg-[#F5F3FF] text-left border-l-4 border-blue-500">
                    <p class="text-xl text-gray-800">Regular system checks and audits</p>
                </div>
                <div class="p-8 rounded-2xl bg-[#FFF7ED] text-left border-l-4 border-blue-500">
                    <p class="text-xl text-gray-800">Instant withdrawals, just minutes away</p>
                </div>
            </div>
        </div>
    </section>

    {{-- NEWS & INSIGHTS --}}
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="bg-blue-50 rounded-[3rem] p-12 lg:p-20 relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-6xl font-black text-[#262262] uppercase mb-4">News & Insights</h2>
                    <p class="text-2xl text-gray-600 mb-12">Stay ahead with the latest in staking and stablecoins.</p>

                    <div class="mt-20">
                        <h3 class="text-5xl font-black text-blue-600 uppercase mb-4">COMING SOON</h3>
                        <p class="text-xl text-gray-600 max-w-xl">
                            Lumastake Academy вЂ” tips, guides and expert interviews to help you make smarter crypto moves.
                        </p>
                    </div>
                </div>
                <div class="absolute right-0 bottom-0 translate-y-1/4 translate-x-1/4">
                    {{-- Placeholder for circular graphic --}}
                    <div class="w-[500px] h-[500px] rounded-full border-[60px] border-blue-100 opacity-50"></div>
                </div>
            </div>
        </div>
    </section>

    {{-- TEAM --}}
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="grid md:grid-cols-3 gap-16">
                <div class="space-y-6">
                    <div class="relative inline-block">
                        <img src="{{ asset('img/1eba7b06db82130b31223b8d328249bd358b9f7c.png') }}" alt="Alan Campbell" class="w-64 h-64 object-cover rounded-3xl">
                    </div>
                    <div>
                        <h3 class="text-3xl font-bold text-[#262262]">Alan Campbell</h3>
                        <p class="text-xl text-blue-600">CEO</p>
                    </div>
                </div>
                <div class="space-y-6">
                    <div class="relative inline-block">
                        <img src="{{ asset('img/5bd9b711a691c8c86e603b5d57afe5528d0dfe76.png') }}" alt="Emily Rossi" class="w-64 h-64 object-cover rounded-3xl">
                    </div>
                    <div>
                        <h3 class="text-3xl font-bold text-[#262262]">Emily Rossi</h3>
                        <p class="text-xl text-blue-600">CFO</p>
                    </div>
                </div>
                <div class="space-y-6">
                    <div class="relative inline-block">
                        <img src="{{ asset('img/3dad6f794de904997b02ed4b1d2ae55abf19ec0b.png') }}" alt="Christopher Taylor" class="w-64 h-64 object-cover rounded-3xl">
                    </div>
                    <div>
                        <h3 class="text-3xl font-bold text-[#262262]">Christopher Taylor</h3>
                        <p class="text-xl text-blue-600">COO</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

