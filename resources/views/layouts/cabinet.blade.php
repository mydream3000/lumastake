@props(['showTopBar' => true])

@php
    $user = auth()->user();
    $currentTier = \App\Models\Tier::find($user->current_tier);
    $allTiers = \App\Models\Tier::orderBy('level')->get();

    // FIX: Re-added this logic here to fix the undefined variable error
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

    <!-- Favicon -->
    <link rel="shortcut icon" href="/img/favicon.png" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/cabinet.css', 'resources/js/cabinet.js'])
</head>
<body class="font-sans antialiased bg-cabinet-dark text-cabinet-text-dark overflow-x-hidden" x-data="{
    mobileMenuOpen: false,
    rightbarOpen: '',
    openRightbar(name) { this.rightbarOpen = name; },
    closeRightbar() { this.rightbarOpen = ''; }
}" x-on:open-rightbar.window="openRightbar($event.detail.name)" x-on:close-rightbar.window="closeRightbar()">

<!-- Admin Impersonation Banner -->
@if(($impersonating ?? false) && !$user->is_admin)
    <div class="fixed top-0 left-0 right-0 bg-cabinet-blue text-white px-4 py-3 z-50 shadow-lg">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                <span class="font-semibold">Вы вошли под пользователем: {{ $user->name }} (ID: {{ $user->id }})</span>
            </div>
            <form method="POST" action="{{ route('admin.return-to-admin') }}" class="inline">
                @csrf
                <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-white text-cabinet-blue rounded-md font-semibold text-sm hover:bg-blue-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Вернуться в админку
                </button>
            </form>
        </div>
    </div>
@endif

@if($showTopBar)
<!-- Mobile Top Header (Fixed) -->
<div class="lg:hidden fixed top-0 left-0 right-0 bg-cabinet-dark px-4 py-3 z-30 w-full border-b border-cabinet-grey {{ (($impersonating ?? false) && !$user->is_admin) ? 'top-14' : '' }}">
    <div class="flex items-center justify-between gap-3">
        <a href="{{ route('cabinet.dashboard') }}"><img src="{{ asset('images/sidebar/logo-white.png') }}" alt="Lumastake" class="h-6"></a>
        <div class="flex items-center gap-2 flex-1 justify-center">
            <button type="button" x-on:click="$dispatch('open-rightbar', {name: 'deposit-sidebar'})" class="px-3 py-1.5 bg-cabinet-blue text-white rounded-md font-semibold text-xs uppercase hover:bg-cabinet-blue/90 transition">Deposit</button>
            <button type="button" x-on:click="$dispatch('open-rightbar', {name: 'withdraw-sidebar'})" class="px-3 py-1.5 bg-cabinet-lime text-cabinet-dark rounded-md font-semibold text-xs uppercase hover:bg-cabinet-lime/90 transition">Withdraw</button>
        </div>
        <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg></button>
    </div>
</div>

<!-- Mobile Sidebar Menu -->
<div x-show="mobileMenuOpen" class="fixed inset-0 z-40 lg:hidden" style="display: none;">
    <div @click="mobileMenuOpen = false" class="fixed inset-0 bg-black/50"></div>
    <div @click.away="mobileMenuOpen = false" class="fixed left-0 top-0 bottom-0 w-80 bg-cabinet-dark z-50 shadow-2xl">
        <div class="bg-cabinet-dark px-4 py-4 flex items-center justify-between border-b border-cabinet-grey">
            <a href="{{ route('cabinet.dashboard') }}" @click="mobileMenuOpen = false"><img src="{{ asset('images/sidebar/logo-white.png') }}" alt="Lumastake" class="h-8"></a>
            <button @click="mobileMenuOpen = false" class="text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
        </div>
        <nav class="py-6" x-data="{ transactionOpen: {{ $isTransactionActive ? 'true' : 'false' }}, earningOpen: {{ $isEarningsActive ? 'true' : 'false' }} }">
            <x-cabinet.mobile-nav-link :href="route('cabinet.dashboard')" :active="request()->routeIs('cabinet.dashboard')">Dashboard</x-cabinet.mobile-nav-link>
            <x-cabinet.mobile-nav-link :href="route('cabinet.pools.index')" :active="request()->routeIs('cabinet.pools.*')">Pools</x-cabinet.mobile-nav-link>
            <x-cabinet.mobile-nav-link :href="route('cabinet.staking.index')" :active="request()->routeIs('cabinet.staking.*')">Stakings</x-cabinet.mobile-nav-link>
            <x-cabinet.mobile-nav-link :href="route('cabinet.history')" :active="request()->routeIs('cabinet.history')">Stake History</x-cabinet.mobile-nav-link>
            <div>
                <button @click="transactionOpen = !transactionOpen" class="flex items-center justify-between w-full gap-4 px-6 py-3 text-cabinet-text-dark hover:bg-cabinet-blue/20 transition-colors {{ $isTransactionActive ? 'sidebar-active' : '' }}">
                    <span class="text-lg font-medium">Transactions</span>
                    <svg class="w-5 h-5 transition-transform" :class="{'rotate-180': transactionOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="transactionOpen" class="pl-16 space-y-1 pb-2">
                    <a href="{{ route('cabinet.transactions.deposits') }}" class="block py-2 text-cabinet-text-grey hover:text-cabinet-blue transition-colors {{ request()->routeIs('cabinet.transactions.deposits') ? 'text-cabinet-blue font-medium' : '' }}">Deposits</a>
                    <a href="{{ route('cabinet.transactions.withdraw') }}" class="block py-2 text-cabinet-text-grey hover:text-cabinet-blue transition-colors {{ request()->routeIs('cabinet.transactions.withdraw') ? 'text-cabinet-blue font-medium' : '' }}">Withdrawals</a>
                </div>
            </div>
            <div>
                <button @click="earningOpen = !earningOpen" class="flex items-center justify-between w-full gap-4 px-6 py-3 text-cabinet-text-dark hover:bg-cabinet-blue/20 transition-colors {{ $isEarningsActive ? 'sidebar-active' : '' }}">
                    <span class="text-lg font-medium">Earnings</span>
                    <svg class="w-5 h-5 transition-transform" :class="{'rotate-180': earningOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="earningOpen" class="pl-16 space-y-1 pb-2">
                    <a href="{{ route('cabinet.earnings.profit') }}" class="block py-2 text-cabinet-text-grey hover:text-cabinet-blue transition-colors {{ request()->routeIs('cabinet.earnings.profit') ? 'text-cabinet-blue font-medium' : '' }}">Profits</a>
                    <a href="{{ route('cabinet.earnings.rewards') }}" class="block py-2 text-cabinet-text-grey hover:text-cabinet-blue transition-colors {{ request()->routeIs('cabinet.earnings.rewards') ? 'text-cabinet-blue font-medium' : '' }}">Rewards</a>
                </div>
            </div>
            <x-cabinet.mobile-nav-link :href="route('cabinet.rewards')" :active="request()->routeIs('cabinet.rewards')">Rewards</x-cabinet.mobile-nav-link>
            <x-cabinet.mobile-nav-link :href="route('cabinet.profile.show')" :active="request()->routeIs('cabinet.profile.*')">Profile</x-cabinet.mobile-nav-link>
        </nav>
    </div>
</div>

<!-- Desktop Top Header -->
<div class="hidden lg:flex items-center justify-between bg-cabinet-dark p-6 border-b border-cabinet-grey fixed top-0 left-[346px] right-0 z-30 {{ (($impersonating ?? false) && !$user->is_admin) ? 'top-14' : '' }}">
    <div class="flex items-center gap-4">
        <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-14 h-14 rounded-full">
        <div>
            <div class="text-2xl font-semibold text-cabinet-blue flex items-center gap-2">Welcome Back <span>👋</span></div>
            <div class="text-base text-cabinet-text-dark font-medium">{{ $user->name }} | <span class="text-cabinet-lime font-extrabold">{{ $currentTier->name ?? 'No Tier' }}</span></div>
        </div>
    </div>
    <div class="flex items-center gap-3">
        @if($user->is_admin)
            <a href="{{ route('admin.dashboard') }}" class="px-6 py-2.5 bg-cabinet-blue text-white rounded-md font-semibold text-sm uppercase hover:bg-cabinet-blue/90 transition">Admin Panel</a>
        @endif
        <button type="button" x-on:click="$dispatch('open-rightbar', {name: 'deposit-sidebar'})" class="px-6 py-2.5 bg-cabinet-blue text-white rounded-md font-semibold text-sm uppercase hover:bg-cabinet-blue/90 transition">Deposit</button>
        <button type="button" x-on:click="$dispatch('open-rightbar', {name: 'withdraw-sidebar'})" class="px-6 py-2.5 bg-cabinet-lime text-cabinet-dark rounded-md font-semibold text-sm uppercase hover:bg-cabinet-lime/90 transition">Withdraw</button>
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="p-2.5 rounded-full bg-cabinet-grey/20 hover:bg-cabinet-grey/40 inline-block transition">
                <svg class="w-5 h-5 text-cabinet-text-grey" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </button>
        </form>
    </div>
</div>
@endif

<div class="flex min-h-screen bg-cabinet-dark {{ (($impersonating ?? false) && !$user->is_admin) ? 'pt-14' : '' }}">
    <!-- Sidebar -->
    <x-cabinet.sidebar />

    <div class="flex-1 flex flex-col min-w-0">
        <!-- Page Content -->
        <main class="flex-1 bg-cabinet-dark pt-[52px] lg:pt-[98px] w-full">
            <div class="container mx-auto px-4 lg:px-6 py-4 lg:py-8">
                {{ $slot }}
            </div>
        </main>
    </div>
</div>

<!-- Sidebars and Modals -->
<x-cabinet.rightbar name="deposit-sidebar" title="Deposit" />
<x-cabinet.rightbar name="withdraw-sidebar" title="Withdraw" />
@foreach($allTiers as $tier)
    <x-cabinet.rightbar name="tier-{{ $tier->id }}" :title="$tier->name" />
@endforeach
<x-toast />
<x-universal-modal />

@stack('scripts')

</body>
</html>
