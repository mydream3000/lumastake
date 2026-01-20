@props(['showTopBar' => true])

@php
    $user = auth()->user();
    $currentTier = \App\Models\Tier::find($user->current_tier);
    $allTiers = \App\Models\Tier::orderBy('level')->get();
    $currentRoute = request()->route()->getName();
    $transactionRoutes = ['cabinet.transactions.deposits', 'cabinet.transactions.withdraw'];
    $earningsRoutes = ['cabinet.earnings.profit', 'cabinet.earnings.rewards'];
    $isTransactionActive = in_array($currentRoute, $transactionRoutes);
    $isEarningsActive = in_array($currentRoute, $earningsRoutes);
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="shortcut icon" href="/img/favicon.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Manrope:wght@200..800&display=swap" rel="stylesheet">
    @php
        if (app()->environment('production')) {
            \Illuminate\Support\Facades\Vite::useHotFile(base_path('no-vite-hot'));
            \Illuminate\Support\Facades\Vite::useBuildDirectory('build');
        }
    @endphp
    @vite(['resources/css/cabinet.css', 'resources/js/cabinet.js'])
</head>
<body class="font-sans antialiased bg-cabinet-bg text-cabinet-text-main overflow-x-hidden" x-data="{ mobileMenuOpen: false, rightbarOpen: '', openRightbar(name) { this.rightbarOpen = name; }, closeRightbar() { this.rightbarOpen = ''; } }" x-on:open-rightbar.window="openRightbar($event.detail.name)" x-on:close-rightbar.window="closeRightbar()">

@if($impersonating ?? false)
    <div class="bg-red-600 text-white py-2 px-4 flex items-center justify-center gap-4 fixed top-0 left-0 right-0 z-[100] h-14">
        <span class="font-bold text-sm md:text-base">Impersonating: {{ auth()->user()->name }}</span>
        <form action="{{ route('admin.return-to-admin') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="bg-white text-red-600 px-3 py-1 rounded-md text-xs md:text-sm font-bold hover:bg-gray-100 transition whitespace-nowrap">
                Back to Admin
            </button>
        </form>
    </div>
@endif

@if($showTopBar)
<!-- Mobile Top Header -->
<div class="lg:hidden fixed left-0 right-0 bg-white px-4 py-3 z-30 w-full border-b border-cabinet-border {{ ($impersonating ?? false) ? 'top-14' : 'top-0' }}">
    <div class="flex items-center justify-between gap-3">
        <a href="{{ route('cabinet.dashboard') }}"><img src="{{ asset('images/sidebar/logo-white.png') }}" alt="Lumastake" class="h-6 brightness-0"></a>
        <div class="flex items-center gap-2 flex-1 justify-center">
            <button type="button" x-on:click="$dispatch('open-rightbar', {name: 'deposit-sidebar'})" class="px-3 py-1.5 bg-cabinet-blue text-white rounded-md font-semibold text-xs uppercase hover:bg-cabinet-blue/90 transition">Deposit</button>
            <button type="button" x-on:click="$dispatch('open-rightbar', {name: 'withdraw-sidebar'})" class="px-3 py-1.5 bg-cabinet-lime text-cabinet-text-main rounded-md font-semibold text-xs uppercase hover:bg-cabinet-lime/90 transition">Withdraw</button>
        </div>
        <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-cabinet-text-main"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg></button>
    </div>
</div>

<!-- Mobile Sidebar Menu -->
<div x-show="mobileMenuOpen" class="fixed inset-0 z-40 lg:hidden" style="display: none;">
    <div @click="mobileMenuOpen = false" class="fixed inset-0 bg-black/50"></div>
    <div @click.away="mobileMenuOpen = false" class="fixed left-0 top-0 bottom-0 w-80 bg-gradient-to-b from-cabinet-sidebar-start to-cabinet-sidebar-end z-50 shadow-2xl">
        <div class="px-4 py-4 flex items-center justify-between border-b border-white/20">
            <a href="{{ route('cabinet.dashboard') }}" @click="mobileMenuOpen = false"><img src="{{ asset('images/sidebar/logo-white.png') }}" alt="Lumastake" class="h-8"></a>
            <button @click="mobileMenuOpen = false" class="text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
        </div>
        <nav class="py-6" x-data="{ transactionOpen: {{ $isTransactionActive ? 'true' : 'false' }}, earningOpen: {{ $isEarningsActive ? 'true' : 'false' }} }">
            <a href="{{ route('cabinet.dashboard') }}" class="flex items-center gap-4 px-6 py-3 text-white hover:bg-white/10 transition-colors {{ request()->routeIs('cabinet.dashboard') ? 'sidebar-active' : '' }}"><span class="text-lg font-medium">Dashboard</span></a>
            <a href="{{ route('cabinet.pools.index') }}" class="flex items-center gap-4 px-6 py-3 text-white hover:bg-white/10 transition-colors {{ request()->routeIs('cabinet.pools.*') ? 'sidebar-active' : '' }}"><span class="text-lg font-medium">Pools</span></a>
            <a href="{{ route('cabinet.staking.index') }}" class="flex items-center gap-4 px-6 py-3 text-white hover:bg-white/10 transition-colors {{ request()->routeIs('cabinet.staking.*') ? 'sidebar-active' : '' }}"><span class="text-lg font-medium">Stakings</span></a>
            <a href="{{ route('cabinet.history') }}" class="flex items-center gap-4 px-6 py-3 text-white hover:bg-white/10 transition-colors {{ request()->routeIs('cabinet.history') ? 'sidebar-active' : '' }}"><span class="text-lg font-medium">Stake History</span></a>
            <div>
                <button @click="transactionOpen = !transactionOpen" class="flex items-center justify-between w-full gap-4 px-6 py-3 text-white hover:bg-white/10 transition-colors {{ $isTransactionActive ? 'sidebar-active' : '' }}">
                    <span class="text-lg font-medium">Transactions</span>
                    <svg class="w-5 h-5 transition-transform" :class="{'rotate-180': transactionOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="transactionOpen" class="pl-16 space-y-1 pb-2">
                    <a href="{{ route('cabinet.transactions.deposits') }}" class="block py-2 text-white/70 hover:text-white transition-colors {{ request()->routeIs('cabinet.transactions.deposits') ? 'text-cabinet-lime font-medium' : '' }}">Deposits</a>
                    <a href="{{ route('cabinet.transactions.withdraw') }}" class="block py-2 text-white/70 hover:text-white transition-colors {{ request()->routeIs('cabinet.transactions.withdraw') ? 'text-cabinet-lime font-medium' : '' }}">Withdrawals</a>
                </div>
            </div>
            <div>
                <button @click="earningOpen = !earningOpen" class="flex items-center justify-between w-full gap-4 px-6 py-3 text-white hover:bg-white/10 transition-colors {{ $isEarningsActive ? 'sidebar-active' : '' }}">
                    <span class="text-lg font-medium">Earnings</span>
                    <svg class="w-5 h-5 transition-transform" :class="{'rotate-180': earningOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="earningOpen" class="pl-16 space-y-1 pb-2">
                    <a href="{{ route('cabinet.earnings.profit') }}" class="block py-2 text-white/70 hover:text-white transition-colors {{ request()->routeIs('cabinet.earnings.profit') ? 'text-cabinet-lime font-medium' : '' }}">Profits</a>
                    <a href="{{ route('cabinet.earnings.rewards') }}" class="block py-2 text-white/70 hover:text-white transition-colors {{ request()->routeIs('cabinet.earnings.rewards') ? 'text-cabinet-lime font-medium' : '' }}">Rewards</a>
                </div>
            </div>
            <a href="{{ route('cabinet.rewards') }}" class="flex items-center gap-4 px-6 py-3 text-white hover:bg-white/10 transition-colors {{ request()->routeIs('cabinet.rewards') ? 'sidebar-active' : '' }}"><span class="text-lg font-medium">Rewards</span></a>
            <a href="{{ route('cabinet.profile.show') }}" class="flex items-center gap-4 px-6 py-3 text-white hover:bg-white/10 transition-colors {{ request()->routeIs('cabinet.profile.*') ? 'sidebar-active' : '' }}"><span class="text-lg font-medium">Profile</span></a>
        </nav>
    </div>
</div>

<!-- Desktop Top Header -->
<div class="hidden lg:flex items-center justify-between bg-white p-6 border-b border-cabinet-border fixed left-[346px] right-0 z-30 {{ ($impersonating ?? false) ? 'top-14' : 'top-0' }}">
    <div class="flex items-center gap-4">
        <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-14 h-14 rounded-full">
        <div>
            <div class="text-2xl font-semibold text-cabinet-blue flex items-center gap-2">Welcome Back <span>👋</span></div>
            <div class="text-base text-cabinet-text-main font-medium">
                {{ $user->name }} |
                <button
                    type="button"
                    @if($currentTier) x-on:click="$dispatch('open-rightbar', {name: 'tier-{{ $currentTier->id }}'})" @endif
                    class="text-cabinet-blue font-extrabold hover:underline"
                >
                    {{ $currentTier->name ?? 'No Tier' }}
                </button>
            </div>
        </div>
    </div>
    <div class="flex items-center gap-3">
        @if($user->is_admin)
            <a href="{{ route('admin.dashboard') }}" class="px-6 py-2.5 bg-cabinet-blue text-white rounded-md font-semibold text-sm uppercase hover:bg-cabinet-blue/90 transition">Admin Panel</a>
        @endif
        <button type="button" x-on:click="$dispatch('open-rightbar', {name: 'deposit-sidebar'})" class="px-6 py-2.5 bg-cabinet-blue text-white rounded-md font-semibold text-sm uppercase hover:bg-cabinet-blue/90 transition">Deposit</button>
        <button type="button" x-on:click="$dispatch('open-rightbar', {name: 'withdraw-sidebar'})" class="px-6 py-2.5 bg-cabinet-lime text-cabinet-text-main rounded-md font-semibold text-sm uppercase hover:bg-cabinet-lime/90 transition">Withdraw</button>
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="p-2.5 rounded-full bg-gray-100 hover:bg-gray-200 inline-block transition text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </button>
        </form>
    </div>
</div>
@endif

<div class="flex min-h-screen bg-cabinet-bg {{ ($impersonating ?? false) ? 'pt-14' : '' }}">
    <!-- Sidebar -->
    <x-cabinet.sidebar />

    <div class="flex-1 flex flex-col min-w-0">
        <!-- Page Content -->
        <main class="flex-1 bg-cabinet-bg pt-[52px] lg:pt-[98px] w-full">
            <div class="px-4 lg:px-8 py-4 lg:py-8">
                {{ $slot }}
            </div>
        </main>
    </div>
</div>

<!-- Sidebars and Modals -->
<x-cabinet.rightbar name="deposit-sidebar" title="Deposit" />
<x-cabinet.rightbar name="withdraw-sidebar" title="Withdraw" />
@foreach($allTiers as $tier)
    @php
        $tierColors = [
            'Starter' => '#F6BD0E',
            'Bronze' => '#FF613E',
            'Silver' => '#F70808',
            'Gold' => '#E10495',
            'Platinum' => '#C000CA',
            'Titanium' => '#2F7DEA',
            'Diamond' => '#1393AF',
            'Elite Diamond' => '#29BDB4',
            'Crown Elite' => '#82CA8A',
            'Royal Legacy' => '#CACA33',
        ];
        $tierColor = $tierColors[$tier->name] ?? '#3B4EFC';
    @endphp
    <x-cabinet.rightbar name="tier-{{ $tier->id }}" :title="$tier->name" :titleColor="$tierColor">
        {{-- Balance Range --}}
        <div class="mb-6">
            <p class="text-xl font-bold text-cabinet-text-main">
                ${{ number_format($tier->min_balance, 0) }} – ${{ number_format($tier->max_balance ?? 999999, 0) }} USDT
            </p>
        </div>

        {{-- Tier Percentages --}}
        <div class="space-y-3">
            @php
                $percentages = $user->account_type === 'islamic'
                    ? $tier->islamicPercentages()->orderBy('duration_days')->get()
                    : $tier->percentages()->orderBy('days')->get();
            @endphp

            @forelse($percentages as $percentage)
                @php
                    $days = $user->account_type === 'islamic' ? $percentage->duration_days : $percentage->days;
                    $percent = $user->account_type === 'islamic'
                        ? $percentage->min_percentage . '% - ' . $percentage->max_percentage . '%'
                        : $percentage->percentage . '%';
                @endphp
                <div class="relative rounded-lg " style="background-color: {{ $tierColor }}1A;">
                    {{-- Left accent bar with diamond --}}
                    <div class="absolute left-0 top-0 bottom-0 w-1" style="background-color: {{ $tierColor }};"></div>
                    <div class="absolute -left-[2px] top-1/2 -translate-y-1/2 -translate-x-1/2">
                        <div class="w-3 h-3 rotate-45 border-2 border-white" style="background-color: {{ $tierColor }};"></div>
                    </div>

                    {{-- Content --}}
                    <div class="pl-5 pr-4 py-3">
                        <div class="flex items-center justify-between mb-1 pb-2 border-b border-dashed border-b-gray-800">
                            <span class="font-bold text-cabinet-text-main">{{ $tier->name }}</span>
                            <span class="text-sm text-gray-500">{{ $days }} Days</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Return On Investment</span>
                            <span class="font-bold" style="color: {{ $tierColor }};">{{ $percent }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-sm">No investment pools available for this tier.</p>
            @endforelse
        </div>
    </x-cabinet.rightbar>
@endforeach
<x-toast />
<x-universal-modal />

@stack('scripts')

</body>
</html>
