@props(['showTopBar' => true])

@php
    $user = auth()->user();
    $currentTier = \App\Models\Tier::find($user->current_tier);
    $allTiers = \App\Models\Tier::orderBy('level')->get();

    // --- FIX: Define variables for active menu state ---
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
    @php
        // Safety fallback: on production force using built assets even if a stray hot file exists
        if (app()->environment('production')) {
            \Illuminate\Support\Facades\Vite::useHotFile(base_path('no-vite-hot'));
            \Illuminate\Support\Facades\Vite::useBuildDirectory('build');
        }
    @endphp

    @vite(['resources/css/cabinet.css', 'resources/js/cabinet.js'])
</head>
<body class="font-sans antialiased bg-cabinet-dark text-cabinet-text-dark overflow-x-hidden" x-data="{
    mobileMenuOpen: false,
    rightbarOpen: '',
    openRightbar(name) {
        this.rightbarOpen = name;
    },
    closeRightbar() {
        this.rightbarOpen = '';
    }
}" x-init="
    // Initialize balance store
    Alpine.store('userBalance', {
        balance: {{ $user->balance }},
        availableBalance: {{ $user->available_balance }},
    });
" x-on:open-rightbar.window="openRightbar($event.detail.name)" x-on:close-rightbar.window="closeRightbar()">
<!-- Admin Impersonation Banner -->
@if(($impersonating ?? false) && !$user->is_admin)
<div class="fixed top-0 left-0 right-0 bg-cabinet-blue text-white px-4 py-3 z-50 shadow-lg">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <span class="font-semibold">Вы вошли под пользователем: {{ $user->name }} (ID: {{ $user->id }})</span>
        </div>
        <form method="POST" action="{{ route('admin.return-to-admin') }}" class="inline">
            @csrf
            <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-white text-cabinet-blue rounded-md font-semibold text-sm hover:bg-blue-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
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
                    <!-- Logo -->
                    <a href="{{ route('cabinet.dashboard') }}">
                        <img src="{{ asset('images/sidebar/logo-white.png') }}" alt="Lumastake" class="h-6">
                    </a>

                    <!-- Deposit/Withdraw Buttons -->
                    <div class="flex items-center gap-2 flex-1 justify-center">
                        <button type="button" x-on:click="$dispatch('open-rightbar', {name: 'deposit-sidebar'})" class="px-3 py-1.5 bg-cabinet-blue text-white rounded-md font-semibold text-xs uppercase hover:bg-cabinet-blue/90 transition">
                            Deposit
                        </button>
                        <button type="button" x-on:click="$dispatch('open-rightbar', {name: 'withdraw-sidebar'})" class="px-3 py-1.5 bg-cabinet-lime text-cabinet-dark rounded-md font-semibold text-xs uppercase hover:bg-cabinet-lime/90 transition">
                            Withdraw
                        </button>
                    </div>

                    <!-- Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
</div>

<!-- Mobile Sidebar Menu Overlay -->
<div x-show="mobileMenuOpen"
     @click="mobileMenuOpen = false"
     x-transition:enter="transition-opacity ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black/50 z-40 lg:hidden"
     style="display: none;">
</div>

<!-- Mobile Sidebar Menu -->
<div x-show="mobileMenuOpen"
     @click.away="mobileMenuOpen = false"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="-translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-full"
     class="fixed left-0 top-0 bottom-0 w-80 bg-cabinet-dark z-50 lg:hidden shadow-2xl"
     style="display: none;">
                    <!-- Menu Header -->
                    <div class="bg-cabinet-dark px-4 py-4 flex items-center justify-between border-b border-cabinet-grey">
                        <a href="{{ route('cabinet.dashboard') }}" @click="mobileMenuOpen = false">
                            <img src="{{ asset('images/sidebar/logo-white.png') }}" alt="Lumastake" class="h-8">
                        </a>
                        <button @click="mobileMenuOpen = false" class="text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Menu Items -->
                    <nav class="py-6" x-data="{ transactionOpen: {{ $isTransactionActive ? 'true' : 'false' }}, earningOpen: {{ $isEarningsActive ? 'true' : 'false' }} }">
                        <a href="{{ route('cabinet.dashboard') }}" class="flex items-center gap-4 px-6 py-3 text-cabinet-text-dark hover:bg-cabinet-blue/20 active:bg-cabinet-blue/30 transition-colors {{ request()->routeIs('cabinet.dashboard') ? 'sidebar-active' : '' }}">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                            </svg>
                            <span class="text-lg font-medium">Dashboard</span>
                        </a>

                        <a href="{{ route('cabinet.pools.index') }}" class="flex items-center gap-4 px-6 py-3 text-cabinet-text-dark hover:bg-cabinet-blue/20 active:bg-cabinet-blue/30 transition-colors {{ request()->routeIs('cabinet.pools.*') ? 'sidebar-active' : '' }}">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.73-2.77-.01-2.2-1.9-2.96-3.66-3.42z"/>
                            </svg>
                            <span class="text-lg font-medium">Pools</span>
                        </a>

                        <a href="{{ route('cabinet.staking.index') }}" class="flex items-center gap-4 px-6 py-3 text-cabinet-text-dark hover:bg-cabinet-blue/20 active:bg-cabinet-blue/30 transition-colors {{ request()->routeIs('cabinet.staking.*') ? 'sidebar-active' : '' }}">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/>
                            </svg>
                            <span class="text-lg font-medium">Stakings</span>
                        </a>

                        <a href="{{ route('cabinet.history') }}" class="flex items-center gap-4 px-6 py-3 text-cabinet-text-dark hover:bg-cabinet-blue/20 active:bg-cabinet-blue/30 transition-colors {{ request()->routeIs('cabinet.history') ? 'sidebar-active' : '' }}">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/>
                            </svg>
                            <span class="text-lg font-medium">Stake History</span>
                        </a>

                        <!-- Transactions (Dropdown) -->
                        <div>
                            <button @click="transactionOpen = !transactionOpen" class="flex items-center justify-between w-full gap-4 px-6 py-3 text-cabinet-text-dark hover:bg-cabinet-blue/20 active:bg-cabinet-blue/30 transition-colors {{ $isTransactionActive ? 'sidebar-active' : '' }}">
                                <div class="flex items-center gap-4">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 14V6c0-1.1-.9-2-2-2H3c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zm-9-1c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm13-6v11c0 1.1-.9 2-2 2H4v-2h17V7h2z"/>
                                    </svg>
                                    <span class="text-lg font-medium">Transactions</span>
                                </div>
                                <svg class="w-5 h-5 transition-transform" :class="{'rotate-180': transactionOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="transactionOpen" class="pl-16 space-y-1 pb-2" style="display: none;">
                                <a href="{{ route('cabinet.transactions.deposits') }}" class="block py-2 text-cabinet-text-grey hover:text-cabinet-blue transition-colors {{ $currentRoute === 'cabinet.transactions.deposits' ? 'text-cabinet-blue font-medium' : '' }}">
                                    Deposits
                                </a>
                                <a href="{{ route('cabinet.transactions.withdraw') }}" class="block py-2 text-cabinet-text-grey hover:text-cabinet-blue transition-colors {{ $currentRoute === 'cabinet.transactions.withdraw' ? 'text-cabinet-blue font-medium' : '' }}">
                                    Withdrawals
                                </a>
                            </div>
                        </div>

                        <!-- Earnings (Dropdown) -->
                        <div>
                            <button @click="earningOpen = !earningOpen" class="flex items-center justify-between w-full gap-4 px-6 py-3 text-cabinet-text-dark hover:bg-cabinet-blue/20 active:bg-cabinet-blue/30 transition-colors {{ $isEarningsActive ? 'sidebar-active' : '' }}">
                                <div class="flex items-center gap-4">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                                    </svg>
                                    <span class="text-lg font-medium">Earnings</span>
                                </div>
                                <svg class="w-5 h-5 transition-transform" :class="{'rotate-180': earningOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="earningOpen" class="pl-16 space-y-1 pb-2" style="display: none;">
                                <a href="{{ route('cabinet.earnings.profit') }}" class="block py-2 text-cabinet-text-grey hover:text-cabinet-blue transition-colors {{ $currentRoute === 'cabinet.earnings.profit' ? 'text-cabinet-blue font-medium' : '' }}">
                                    Profits
                                </a>
                                <a href="{{ route('cabinet.earnings.rewards') }}" class="block py-2 text-cabinet-text-grey hover:text-cabinet-blue transition-colors {{ $currentRoute === 'cabinet.earnings.rewards' ? 'text-cabinet-blue font-medium' : '' }}">
                                    Rewards
                                </a>
                            </div>
                        </div>

                        <a href="{{ route('cabinet.rewards') }}" class="flex items-center gap-4 px-6 py-3 text-cabinet-text-dark hover:bg-cabinet-blue/20 active:bg-cabinet-blue/30 transition-colors {{ request()->routeIs('cabinet.rewards') ? 'sidebar-active' : '' }}">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"/>
                            </svg>
                            <span class="text-lg font-medium">Rewards</span>
                        </a>

                        <a href="{{ route('cabinet.profile.show') }}" class="flex items-center gap-4 px-6 py-3 text-cabinet-text-dark hover:bg-cabinet-blue/20 active:bg-cabinet-blue/30 transition-colors {{ request()->routeIs('cabinet.profile.*') ? 'sidebar-active' : '' }}">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                            <span class="text-lg font-medium">Profile</span>
                        </a>
                    </nav>
</div>

<!-- Desktop Top Header -->
<div class="hidden lg:flex items-center justify-between bg-cabinet-dark p-6 border-b border-cabinet-grey fixed top-0 left-[346px] right-0 z-30 {{ (($impersonating ?? false) && !$user->is_admin) ? 'top-14' : '' }}">
                <div class="flex items-center gap-4">
                    <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-14 h-14 rounded-full">
                    <div>
                        <div class="text-2xl font-semibold text-cabinet-blue flex items-center gap-2">
                            Welcome Back <span>👋</span>
                        </div>
                        <div class="text-base text-cabinet-text-dark font-medium">{{ $user->name }} | <span class="text-cabinet-lime font-extrabold" style="text-shadow: 0 2px 4px rgba(227, 255, 59, 0.3);">{{ $currentTier->name ?? 'No Tier' }}</span></div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if($user->is_admin)
                        <a href="{{ route('admin.dashboard') }}" class="px-6 py-2.5 bg-cabinet-blue text-white rounded-md font-semibold text-sm uppercase hover:bg-cabinet-blue/90 transition">
                            Admin Panel
                        </a>
                    @endif
                    <button type="button" x-on:click="$dispatch('open-rightbar', {name: 'deposit-sidebar'})" class="px-6 py-2.5 bg-cabinet-blue text-white rounded-md font-semibold text-sm uppercase hover:bg-cabinet-blue/90 transition">
                        Deposit
                    </button>
                    <button type="button" x-on:click="$dispatch('open-rightbar', {name: 'withdraw-sidebar'})" class="px-6 py-2.5 bg-cabinet-lime text-cabinet-dark rounded-md font-semibold text-sm uppercase hover:bg-cabinet-lime/90 transition">
                        Withdraw
                    </button>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="p-2.5 rounded-full bg-cabinet-grey/20 hover:bg-cabinet-grey/40 inline-block transition">
                            <svg class="w-5 h-5 text-cabinet-text-grey" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
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
            <div class="lg:hidden bg-cabinet-dark px-4 py-6 w-full" x-data="{ userMenuOpen: false }">
                <div class="flex items-center gap-3">
                    <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-10 h-10 rounded-full">
                    <div class="flex-1">
                        <div class="text-base font-extrabold text-cabinet-blue">
                            Welcome Back 👋
                        </div>
                        <div class="text-xs text-cabinet-text-grey">
                            {{ $user->name }} | <span class="text-cabinet-lime font-extrabold" style="text-shadow: 0 1px 2px rgba(227, 255, 59, 0.3);">{{ $currentTier->name ?? 'No Tier' }}</span>
                        </div>
                    </div>
                    <!-- User Menu Button -->
                    <div class="relative">
                        <button @click="userMenuOpen = !userMenuOpen" class="w-7 h-7 bg-cabinet-grey/20 rounded-full flex items-center justify-center">
                            <img src="{{ asset('images/figma/3e82440f81aee75623ddcc012000f5962f025bab.svg') }}" alt="Menu" class="w-4 h-4">
                        </button>

                        <!-- User Dropdown Menu -->
                        <div x-show="userMenuOpen"
                             @click.away="userMenuOpen = false"
                             class="absolute right-0 top-full mt-2 w-40 bg-cabinet-dark rounded-lg shadow-lg border border-cabinet-grey py-1 z-50"
                             style="display: none;">
                            @if($user->is_admin)
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2.5 text-sm text-cabinet-blue hover:bg-cabinet-grey/20 font-medium">
                                    Admin Panel
                                </a>
                            @endif
                            <a href="{{ route('cabinet.feedback') }}" class="block px-4 py-2.5 text-sm text-cabinet-lime hover:bg-cabinet-grey/20 font-medium">
                                Contact Us
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left block px-4 py-2.5 text-sm text-cabinet-red hover:bg-cabinet-grey/20 font-medium">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container mx-auto px-4 lg:px-6 py-4 lg:py-8">
                @if($showTopBar)
                    <!-- Rank Bar -->
                    <div class="bg-cabinet-dark/50 p-3 mb-4 rounded-lg border border-cabinet-grey overflow-x-auto">
                        <div class="flex items-center gap-1.5 text-xs font-semibold min-w-max">
                            @foreach($allTiers as $tier)
                                <button
                                    x-on:click="$dispatch('open-rightbar', {name: 'tier-{{ $tier->id }}'})"
                                    class="flex items-center gap-1 px-2 py-1 hover:opacity-80 transition whitespace-nowrap {{ $currentTier && $tier->id == $currentTier->id ? 'font-extrabold text-cabinet-lime' : 'text-cabinet-text-grey' }}"
                                >
                                    <span>{{ $tier->name }}</span>
                                </button>
                                @if(!$loop->last)
                                    <svg class="w-2.5 h-2.5 text-cabinet-grey flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{ $slot }}
            </div>
        </main>
    </div>
</div>

<!-- Deposit & Withdraw Sidebars -->
<x-cabinet.rightbar name="deposit-sidebar" title="Deposit" />
<x-cabinet.rightbar name="withdraw-sidebar" title="Withdraw" />

<!-- Tier Information Sidebars -->
@foreach($allTiers as $tier)
    <x-cabinet.rightbar name="tier-{{ $tier->id }}" :title="$tier->name" />
@endforeach

<x-toast />
<x-universal-modal />

@stack('scripts')

@if(config('services.intercom.app_id'))
{{-- Intercom Live Chat Widget --}}
<script>
  window.intercomSettings = {
    api_base: "https://api-iam.intercom.io",
    app_id: "{{ config('services.intercom.app_id') }}",
    user_id: {{ auth()->id() }},
    name: @json(auth()->user()->name),
    email: @json(auth()->user()->email),
    @if(auth()->user()->phone)
    phone: @json(auth()->user()->phone),
    @endif
    created_at: {{ auth()->user()->created_at->timestamp }},
  };
</script>
<script>
  (function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',w.intercomSettings);}else{var d=document;var i=function(){i.c(arguments);};i.q=[];i.c=function(args){i.q.push(args);};w.Intercom=i;var l=function(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/{{ config('services.intercom.app_id') }}';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);};if(document.readyState==='complete'){l();}else if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();
</script>
@endif

</body>
</html>
