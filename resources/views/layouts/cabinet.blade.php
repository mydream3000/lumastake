@props(['showTopBar' => true])

@php
    $user = auth()->user();
    // Use Tier id stored in user.current_tier; show No Tier when null
    $currentTier = \App\Models\Tier::find($user->current_tier);
    $allTiers = \App\Models\Tier::orderBy('level')->get();
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
<body class="font-sans antialiased overflow-x-hidden" x-data="{
    mobileMenuOpen: false,
    rightbarOpen: '',
    openRightbar(name) {
        this.rightbarOpen = name;
    },
    closeRightbar() {
        this.rightbarOpen = '';
    }
}" x-init="
    // Initialize balance store (with client-side persistence)
    Alpine.store('userBalance', {
        key: 'arb_pw_u_{{ $user->id }}',
        balance: {{ $user->balance }},
        availableBalance: {{ $user->available_balance }},
        pendingWithdrawalAmount: 0,

        // Amount already reserved server-side (PendingWithdrawal or pending withdraw tx)
        get serverReserved() {
            return Math.max(0, this.balance - this.availableBalance);
        },

        // Show balance minus reservation: avoid double-subtract when server already reserved
        get displayBalance() {
            const toSubtract = Math.max(this.serverReserved, this.pendingWithdrawalAmount);
            return Math.max(0, this.balance - toSubtract);
        },

        // Available balance already excludes serverReserved; subtract only the client-only delta
        get displayAvailableBalance() {
            const clientDelta = Math.max(0, this.pendingWithdrawalAmount - this.serverReserved);
            return Math.max(0, this.availableBalance - clientDelta);
        },

        persist() {
            try {
                if (this.pendingWithdrawalAmount > 0) {
                    sessionStorage.setItem(this.key, String(this.pendingWithdrawalAmount));
                } else {
                    sessionStorage.removeItem(this.key);
                }
            } catch (e) { /* ignore */ }
        },

        load() {
            try {
                const raw = sessionStorage.getItem(this.key);
                const n = parseFloat(raw);
                this.pendingWithdrawalAmount = isNaN(n) ? 0 : n;
            } catch (e) {
                this.pendingWithdrawalAmount = 0;
            }
        },

        setPendingWithdrawal(amount) {
            this.pendingWithdrawalAmount = parseFloat(amount) || 0;
            this.persist();
            console.log('Pending withdrawal set:', this.pendingWithdrawalAmount);
            console.log('Server reserved:', this.serverReserved);
            console.log('Display balance:', this.displayBalance);
            console.log('Display available balance:', this.displayAvailableBalance);
        },

        clearPendingWithdrawal() {
            this.pendingWithdrawalAmount = 0;
            this.persist();
            console.log('Pending withdrawal cleared');
        },

        async sync() {
            try {
                const response = await fetch('{{ route('cabinet.dashboard.balance-state') }}', {
                    headers: { 'Accept': 'application/json' }
                });
                if (!response.ok) return;
                const data = await response.json();
                if (!data || data.success !== true) return;

                const prevBalance = this.balance;
                const prevAvailable = this.availableBalance;
                const prevPending = this.pendingWithdrawalAmount;

                this.balance = parseFloat(data.balance) || 0;
                this.availableBalance = parseFloat(data.available_balance) || 0;
                const serverPending = parseFloat(data.pending_withdraw_amount) || 0;

                if (!data.has_pending_withdraw || serverPending <= 0) {
                    if (this.pendingWithdrawalAmount > 0) {
                        this.clearPendingWithdrawal();
                    }
                } else {
                    // Align client reservation with server-side pending to avoid mismatch
                    this.pendingWithdrawalAmount = serverPending;
                    this.persist();
                }

                console.log('Balance sync:', { prevBalance, prevAvailable, prevPending, newBalance: this.balance, newAvailable: this.availableBalance, serverPending });
            } catch (e) { /* ignore */ }
        }
    });
    Alpine.store('userBalance').load();
    Alpine.store('userBalance').sync();
    // Auto-sync on focus/visibility and periodically
    window.addEventListener('focus', () => Alpine.store('userBalance').sync());
    document.addEventListener('visibilitychange', () => { if (!document.hidden) Alpine.store('userBalance').sync(); });
    setInterval(() => Alpine.store('userBalance').sync(), 45000);
    console.log('Balance store initialized:', Alpine.store('userBalance'));
" x-on:open-rightbar.window="openRightbar($event.detail.name)" x-on:close-rightbar.window="closeRightbar()">
<!-- Admin Impersonation Banner -->
@if(($impersonating ?? false) && !$user->is_admin)
<div class="fixed top-0 left-0 right-0 bg-blue-600 text-white px-4 py-3 z-50 shadow-lg">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <span class="font-semibold">Вы вошли под пользователем: {{ $user->name }} (ID: {{ $user->id }})</span>
        </div>
        <form method="POST" action="{{ route('admin.return-to-admin') }}" class="inline">
            @csrf
            <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-white text-blue-600 rounded-md font-semibold text-sm hover:bg-blue-50 transition">
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
<div class="lg:hidden fixed top-0 left-0 right-0 bg-[#101221] px-4 py-3 z-30 w-full {{ (($impersonating ?? false) && !$user->is_admin) ? 'top-14' : '' }}">
                <div class="flex items-center justify-between gap-3">
                    <!-- Logo -->
                    <a href="{{ route('cabinet.dashboard') }}">
                        <img src="{{ asset('images/sidebar/logo-white.png') }}" alt="Lumastake" class="h-6">
                    </a>

                    <!-- Deposit/Withdraw Buttons -->
                    <div class="flex items-center gap-2 flex-1 justify-center">
                        <button type="button" x-on:click="$dispatch('open-rightbar', {name: 'deposit-sidebar'})" class="px-3 py-1.5 bg-cabinet-orange text-white rounded-md font-semibold text-xs uppercase hover:bg-cabinet-orange/90 transition">
                            Deposit
                        </button>
                        <button type="button" x-on:click="$dispatch('open-rightbar', {name: 'withdraw-sidebar'})" class="px-3 py-1.5 bg-cabinet-green text-white rounded-md font-semibold text-xs uppercase hover:bg-cabinet-green/90 transition">
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
     class="fixed left-0 top-0 bottom-0 w-80 bg-white z-50 lg:hidden shadow-2xl"
     style="display: none;">
                    <!-- Menu Header -->
                    <div class="bg-[#101221] px-4 py-4 flex items-center justify-between">
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
                    <nav class="py-6" x-data="{ transactionOpen: {{ request()->routeIs('cabinet.transactions.*') ? 'true' : 'false' }}, earningOpen: {{ request()->routeIs('cabinet.earnings.*') ? 'true' : 'false' }} }">
                        <a href="{{ route('cabinet.dashboard') }}" class="flex items-center gap-4 px-6 py-3 text-gray-800 hover:bg-gray-50 active:bg-gray-200 transition-colors {{ request()->routeIs('cabinet.dashboard') ? 'bg-gray-50 text-cabinet-orange' : '' }}">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                            </svg>
                            <span class="text-lg font-medium">Dashboard</span>
                        </a>

                        <a href="{{ route('cabinet.pools.index') }}" class="flex items-center gap-4 px-6 py-3 text-gray-800 hover:bg-gray-50 active:bg-gray-200 transition-colors {{ request()->routeIs('cabinet.pools.*') ? 'bg-gray-50 text-cabinet-orange' : '' }}">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.73-2.77-.01-2.2-1.9-2.96-3.66-3.42z"/>
                            </svg>
                            <span class="text-lg font-medium">Pools</span>
                        </a>

                        <a href="{{ route('cabinet.staking.index') }}" class="flex items-center gap-4 px-6 py-3 text-gray-800 hover:bg-gray-50 active:bg-gray-200 transition-colors {{ request()->routeIs('cabinet.staking.*') ? 'bg-gray-50 text-cabinet-orange' : '' }}">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/>
                            </svg>
                            <span class="text-lg font-medium">Stakings</span>
                        </a>

                        <a href="{{ route('cabinet.history') }}" class="flex items-center gap-4 px-6 py-3 text-gray-800 hover:bg-gray-50 active:bg-gray-200 transition-colors {{ request()->routeIs('cabinet.history') ? 'bg-gray-50 text-cabinet-orange' : '' }}">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/>
                            </svg>
                            <span class="text-lg font-medium">Stake History</span>
                        </a>

                        <!-- Transactions (Dropdown) -->
                        <div>
                            <button @click="transactionOpen = !transactionOpen" class="flex items-center justify-between w-full gap-4 px-6 py-3 text-gray-800 hover:bg-gray-50 active:bg-gray-200 transition-colors {{ request()->routeIs('cabinet.transactions.*') ? 'bg-gray-50 text-cabinet-orange' : '' }}">
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
                            <div x-show="transactionOpen"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 -translate-y-2"
                                 class="pl-16 space-y-1 pb-2"
                                 style="display: none;">
                                <a href="{{ route('cabinet.transactions.deposits') }}" class="block py-2 text-gray-700 hover:text-cabinet-orange active:text-cabinet-orange/80 transition-colors {{ request()->routeIs('cabinet.transactions.deposits') ? 'text-cabinet-orange font-medium' : '' }}">
                                    Deposits
                                </a>
                                <a href="{{ route('cabinet.transactions.withdraw') }}" class="block py-2 text-gray-700 hover:text-cabinet-orange active:text-cabinet-orange/80 transition-colors {{ request()->routeIs('cabinet.transactions.withdraw') ? 'text-cabinet-orange font-medium' : '' }}">
                                    Withdrawals
                                </a>
                            </div>
                        </div>

                        <!-- Earnings (Dropdown) -->
                        <div>
                            <button @click="earningOpen = !earningOpen" class="flex items-center justify-between w-full gap-4 px-6 py-3 text-gray-800 hover:bg-gray-50 active:bg-gray-200 transition-colors {{ request()->routeIs('cabinet.earnings.*') ? 'bg-gray-50 text-cabinet-orange' : '' }}">
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
                            <div x-show="earningOpen"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 -translate-y-2"
                                 class="pl-16 space-y-1 pb-2"
                                 style="display: none;">
                                <a href="{{ route('cabinet.earnings.profit') }}" class="block py-2 text-gray-700 hover:text-cabinet-orange active:text-cabinet-orange/80 transition-colors {{ request()->routeIs('cabinet.earnings.profit') ? 'text-cabinet-orange font-medium' : '' }}">
                                    Profits
                                </a>
                                <a href="{{ route('cabinet.earnings.rewards') }}" class="block py-2 text-gray-700 hover:text-cabinet-orange active:text-cabinet-orange/80 transition-colors {{ request()->routeIs('cabinet.earnings.rewards') ? 'text-cabinet-orange font-medium' : '' }}">
                                    Rewards
                                </a>
                            </div>
                        </div>

                        <a href="{{ route('cabinet.rewards') }}" class="flex items-center gap-4 px-6 py-3 text-gray-800 hover:bg-gray-50 active:bg-gray-200 transition-colors {{ request()->routeIs('cabinet.rewards') ? 'bg-gray-50 text-cabinet-orange' : '' }}">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"/>
                            </svg>
                            <span class="text-lg font-medium">Rewards</span>
                        </a>

                        <a href="{{ route('cabinet.profile.show') }}" class="flex items-center gap-4 px-6 py-3 text-gray-800 hover:bg-gray-50 active:bg-gray-200 transition-colors {{ request()->routeIs('cabinet.profile.*') ? 'bg-gray-50 text-cabinet-orange' : '' }}">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                            <span class="text-lg font-medium">Profile</span>
                        </a>
                    </nav>
</div>

<!-- Desktop Top Header -->
<div class="hidden lg:flex items-center justify-between bg-white p-6 border-b border-gray-200 fixed top-0 left-[346px] right-0 z-30 {{ (($impersonating ?? false) && !$user->is_admin) ? 'top-14' : '' }}">
                <div class="flex items-center gap-4">
                    <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-14 h-14 rounded-full">
                    <div>
                        <div class="text-2xl font-semibold text-cabinet-orange flex items-center gap-2">
                            Welcome Back <span>👋</span>
                        </div>
                        <div class="text-base text-gray-800 font-medium">{{ $user->name }} | <span class="text-cabinet-green font-extrabold" style="text-shadow: 0 2px 4px rgba(1, 164, 25, 0.3);">{{ $currentTier->name ?? 'No Tier' }}</span></div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if($user->is_admin)
                        <a href="{{ route('admin.dashboard') }}" class="px-6 py-2.5 bg-blue-600 text-white rounded-md font-semibold text-sm uppercase hover:bg-blue-700 transition">
                            Admin Panel
                        </a>
                    @endif
                    <button type="button" x-on:click="$dispatch('open-rightbar', {name: 'deposit-sidebar'})" class="px-6 py-2.5 bg-cabinet-orange text-white rounded-md font-semibold text-sm uppercase hover:bg-cabinet-orange/90 transition">
                        Deposit
                    </button>
                    <button type="button" x-on:click="$dispatch('open-rightbar', {name: 'withdraw-sidebar'})" class="px-6 py-2.5 bg-cabinet-green text-white rounded-md font-semibold text-sm uppercase hover:bg-cabinet-green/90 transition">
                        Withdraw
                    </button>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="p-2.5 rounded-full bg-gray-100 hover:bg-gray-200 inline-block transition">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                </div>
</div>
@endif

<div class="flex min-h-screen bg-gray-100 {{ (($impersonating ?? false) && !$user->is_admin) ? 'pt-14' : '' }}">
    <!-- Sidebar -->
    <x-cabinet.sidebar />

    <div class="flex-1 flex flex-col min-w-0">
        <!-- Page Content -->
        <main class="flex-1 bg-white pt-[52px] lg:pt-[98px] w-full">
            <div class="lg:hidden bg-white px-4 py-6 w-full" x-data="{ userMenuOpen: false }">
                <div class="flex items-center gap-3">
                    <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-10 h-10 rounded-full">
                    <div class="flex-1">
                        <div class="text-base font-extrabold text-cabinet-orange">
                            Welcome Back 👋
                        </div>
                        <div class="text-xs text-gray-600">
                            {{ $user->name }} | <span class="text-cabinet-green font-extrabold" style="text-shadow: 0 1px 2px rgba(1, 164, 25, 0.3);">{{ $currentTier->name ?? 'No Tier' }}</span>
                        </div>
                    </div>
                    <!-- User Menu Button -->
                    <div class="relative">
                        <button @click="userMenuOpen = !userMenuOpen" class="w-7 h-7 bg-gray-100 rounded-full flex items-center justify-center">
                            <img src="{{ asset('images/figma/3e82440f81aee75623ddcc012000f5962f025bab.svg') }}" alt="Menu" class="w-4 h-4">
                        </button>

                        <!-- User Dropdown Menu -->
                        <div x-show="userMenuOpen"
                             @click.away="userMenuOpen = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 top-full mt-2 w-40 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
                             style="display: none;">
                            @if($user->is_admin)
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2.5 text-sm text-blue-600 hover:bg-gray-50 font-medium">
                                    Admin Panel
                                </a>
                            @endif
                            <a href="{{ route('cabinet.feedback') }}" class="block px-4 py-2.5 text-sm text-cabinet-green hover:bg-gray-50 font-medium">
                                Contact Us
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left block px-4 py-2.5 text-sm text-cabinet-orange hover:bg-gray-50 font-medium">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container mx-auto px-4 lg:px-6 py-4 lg:py-8">
                @if($showTopBar)
                    <!-- Mobile Rank Bar -->
                    <div class="lg:hidden bg-[rgba(16,18,33,0.1)] backdrop-blur p-3 mb-4 rounded-lg border border-[rgba(16,18,33,0.15)] overflow-x-auto">
                        <div class="flex items-center gap-1.5 text-xs font-semibold min-w-max">
                            @foreach($allTiers as $tier)
                                <button
                                    x-on:click="$dispatch('open-rightbar', {name: 'tier-{{ $tier->id }}'})"
                                    class="flex items-center gap-1 px-2 py-1 hover:opacity-80 transition whitespace-nowrap {{ $currentTier && $tier->id == $currentTier->id ? 'font-extrabold' : '' }}"
                                    style="color: {{ $currentTier && $tier->id == $currentTier->id ? '#01a419' : '#444444' }}; {{ $currentTier && $tier->id == $currentTier->id ? 'text-shadow: 0 1px 2px rgba(1, 164, 25, 0.3);' : '' }}"
                                >
                                    <span>{{ $tier->name }}</span>
                                </button>
                                @if(!$loop->last)
                                    <svg class="w-2.5 h-2.5 text-gray-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Desktop Rank Bar -->
                    <div class="hidden lg:block bg-[rgba(16,18,33,0.1)] p-4 mb-6 rounded-lg border border-[rgba(16,18,33,0.15)] overflow-x-auto">
                        <div class="flex justify-between items-center text-sm font-semibold min-w-max gap-2">
                            @foreach($allTiers as $tier)
                                <button
                                    x-on:click="$dispatch('open-rightbar', {name: 'tier-{{ $tier->id }}'})"
                                    class="flex border-l border-gray-700 items-center gap-2 px-3 py-1.5 hover:opacity-80 transition {{ $currentTier && $tier->id == $currentTier->id ? 'font-extrabold' : '' }}"
                                    style="color: {{ $currentTier && $tier->id == $currentTier->id ? '#01a419' : '#444444' }}; {{ $currentTier && $tier->id == $currentTier->id ? 'text-shadow: 0 1px 3px rgba(1, 164, 25, 0.3);' : '' }}"
                                >
                                    <span>{{ $tier->name }}</span>
                                </button>
                                @if(!$loop->last)
                                    <svg width="6" height="7" viewBox="0 0 6 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M5.35732 3.0694C5.65997 3.26666 5.65997 3.70991 5.35732 3.90716L0.773016 6.8951C0.440424 7.11187 -3.05911e-07 6.87321 -2.90539e-07 6.47621L-5.91525e-08 0.50035C-4.37806e-08 0.103349 0.440424 -0.135308 0.773016 0.0814673L5.35732 3.0694Z" fill="#444444"/>
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

<!-- Deposit Sidebar -->
<x-cabinet.rightbar name="deposit-sidebar">
    <x-slot name="title">
        <span x-data="{ get title() {
            const comp = $root.querySelector('[x-ref=depositComponent]')?.__x?.$data;
            if (comp?.selectedToken === 'USDT') return 'Deposit USDT';
            if (comp?.selectedToken === 'USDC') return 'Deposit USDC';
            return 'Deposit Crypto';
        }}" x-text="title">Deposit Crypto</span>
    </x-slot>

    <div x-data="depositUSDT()" x-ref="depositComponent">
        <!-- Loading состояние -->
        <div x-show="loading" class="flex flex-col items-center justify-center py-8">
            <svg class="animate-spin h-10 w-10 text-cabinet-green" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="mt-4 text-gray-600 dark:text-gray-400">Loading...</p>
        </div>

        <!-- Шаг 1: Выбор токена -->
        <div x-show="!loading && step === 1" class="space-y-3">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-700 mb-3">
                Select Token
            </label>

            <!-- USDT -->
            <button
                type="button"
                @click="selectToken('USDT')"
                class="w-full flex items-center gap-3 px-4 py-3 bg-[#fcfcfc] border border-[#eeeeee] rounded-lg transition-all hover:border-cabinet-green hover:bg-cabinet-green/5"
            >
                <img src="{{ asset('images/figma/a6c4f721f402307ba991260820140281a188edd1.png') }}" alt="USDT" class="w-8 h-8">
                <div class="flex-1 text-left">
                    <div class="text-sm font-semibold text-gray-500 dark:text-gray-500">USDT</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Tether USD</div>
                </div>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <!-- USDC -->
            <button
                type="button"
                @click="selectToken('USDC')"
                class="w-full flex items-center gap-3 px-4 py-3 bg-[#fcfcfc] border border-[#eeeeee] rounded-lg transition-all hover:border-cabinet-green hover:bg-cabinet-green/5"
            >
                <img src="{{ asset('img/usd-coin-usdc-logo.svg') }}" alt="USDC" class="w-8 h-8">
                <div class="flex-1 text-left">
                    <div class="text-sm font-semibold text-gray-500 dark:text-gray-500">USDC</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">USD Coin</div>
                </div>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                <p class="text-xs text-gray-600">
                    <strong>Important:</strong> Minimum deposit is 50 USDT/USDC. Deposits below this amount will not be credited.
                </p>
            </div>
        </div>

        <!-- Шаг 2: Выбор сети -->
        <div x-show="!loading && step === 2" class="space-y-4">
            <!-- Кнопка "Назад" -->
            <button
                type="button"
                @click="step = 1; selectedToken = ''; selectedNetwork = ''"
                class="flex items-center gap-2 text-sm text-gray-300 dark:text-gray-400 hover:text-gray-400 dark:hover:text-gray-300 transition mb-4"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to token selection
            </button>

            <!-- Показываем выбранный токен -->
            <div class="flex items-center gap-3 p-3 bg-[#fcfcfc] border border-[#eeeeee] rounded-lg mb-4">
                <img :src="selectedToken === 'USDT' ? '{{ asset('images/figma/a6c4f721f402307ba991260820140281a188edd1.png') }}' : '{{ asset('img/usd-coin-usdc-logo.svg') }}'" alt="" class="w-6 h-6">
                <span class="text-sm font-medium text-gray-400 dark:text-gray-400" x-text="selectedToken"></span>
            </div>

            <!-- Выбор сети -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Network</label>
                <div class="grid grid-cols-2 gap-2">
                    <!-- Ethereum (USDT & USDC) -->
                    <button
                        type="button"
                        x-show="selectedToken === 'USDT' || selectedToken === 'USDC'"
                        @click="selectedNetwork = 'ethereum'"
                        class="flex flex-col items-center gap-1.5 p-2.5 md:p-3 rounded-md border transition-all"
                        :class="selectedNetwork === 'ethereum' ? 'border-cabinet-green bg-cabinet-green/10' : 'border-gray-300 dark:border-gray-600 hover:border-cabinet-green/50'"
                    >
                        <svg class="w-6 h-6 md:w-7 md:h-7" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 32C24.8366 32 32 24.8366 32 16C32 7.16344 24.8366 0 16 0C7.16344 0 0 7.16344 0 16C0 24.8366 7.16344 32 16 32Z" fill="#627EEA"/>
                            <path d="M16.498 4V12.87L23.995 16.22L16.498 4Z" fill="white" fill-opacity="0.602"/>
                            <path d="M16.498 4L9 16.22L16.498 12.87V4Z" fill="white"/>
                            <path d="M16.498 21.968V27.995L24 17.616L16.498 21.968Z" fill="white" fill-opacity="0.602"/>
                            <path d="M16.498 27.995V21.967L9 17.616L16.498 27.995Z" fill="white"/>
                            <path d="M16.498 20.573L23.995 16.22L16.498 12.872V20.573Z" fill="white" fill-opacity="0.2"/>
                            <path d="M9 16.22L16.498 20.573V12.872L9 16.22Z" fill="white" fill-opacity="0.602"/>
                        </svg>
                        <span class="text-xs font-medium text-gray dark:text-gray-400">Ethereum</span>
                        <span class="text-[13px] text-gray dark:text-gray">ERC-20</span>
                    </button>

                    <!-- TRON (только для USDT) -->
                    <button
                        type="button"
                        x-show="selectedToken === 'USDT'"
                        @click="selectedNetwork = 'tron'"
                        class="flex flex-col items-center gap-1.5 p-2.5 md:p-3 rounded-md border transition-all"
                        :class="selectedNetwork === 'tron' ? 'border-cabinet-green bg-cabinet-green/10' : 'border-gray-300 dark:border-gray-600 hover:border-cabinet-green/50'"
                    >
                        <svg class="w-6 h-6 md:w-7 md:h-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 16">
                            <path fill="#ef0027" d="M8 16c4.4183 0 8 -3.5817 8 -8 0 -4.41828 -3.5817 -8 -8 -8C3.58172 0 0 3.58172 0 8c0 4.4183 3.58172 8 8 8Z"></path>
                            <path fill="#ffffff" d="M10.966 4.95654 3.75 3.62854l3.7975 9.55601 5.2915 -6.447 -1.873 -1.78101ZM10.85 5.54155l1.104 1.0495 -3.019 0.5465 1.915 -1.596Zm-2.571 1.4865 -3.182 -2.63901 5.201 0.95701 -2.019 1.682Zm-0.2265 0.467 -0.519 4.29L4.736 4.74354l3.3165 2.75151Zm0.48 0.2275 3.3435 -0.605 -3.835 4.6715 0.4915 -4.0665Z"></path>
                        </svg>
                        <span class="text-xs font-medium text-gray dark:text-gray-400">TRON</span>
                        <span class="text-[13px] text-gray dark:text-gray">TRC-20</span>
                    </button>

                    <!-- BNB Smart Chain (только для USDC) -->
                    <button
                        type="button"
                        x-show="selectedToken === 'USDC'"
                        @click="selectedNetwork = 'bsc'"
                        class="flex flex-col items-center gap-1.5 p-2.5 md:p-3 rounded-md border transition-all"
                        :class="selectedNetwork === 'bsc' ? 'border-cabinet-green bg-cabinet-green/10' : 'border-gray-300 dark:border-gray-600 hover:border-cabinet-green/50'"
                    >
                        <svg class="w-8 h-8" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 32C24.8366 32 32 24.8366 32 16C32 7.16344 24.8366 0 16 0C7.16344 0 0 7.16344 0 16C0 24.8366 7.16344 32 16 32Z" fill="#F3BA2F"/>
                            <path d="M12.116 14.404L16 10.52L19.886 14.404L22.146 12.144L16 6L9.856 12.144L12.116 14.404ZM6 16L8.26 13.74L10.52 16L8.26 18.26L6 16ZM12.116 17.596L16 21.48L19.886 17.596L22.146 19.856L16 26L9.856 19.856L12.116 17.596ZM21.48 16L23.74 13.74L26 16L23.74 18.26L21.48 16ZM16 18.26L13.74 16L16 13.74L18.26 16L16 18.26Z" fill="white"/>
                        </svg>
                        <span class="text-xs font-medium text-gray dark:text-gray-400">BNB Chain</span>
                        <span class="text-[13px] text-gray dark:text-gray">BEP-20</span>
                    </button>
                </div>
            </div>

            <!-- Кнопка подтверждения -->
            <button
                type="button"
                x-on:click="loadDepositAddress()"
                :disabled="!selectedNetwork"
                class="w-full px-4 py-3 bg-cabinet-orange hover:bg-cabinet-orange/90 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-md text-sm font-semibold uppercase transition"
            >
                Get Deposit Address
            </button>
        </div>

        <!-- Шаг 3: Данные адреса (QR код) -->
        <div x-show="!loading && step === 3" class="space-y-6">
            <!-- QR код -->
            <div class="flex justify-center">
                <div class="bg-white p-4 rounded-lg shadow-sm" x-html="qrCode"></div>
            </div>

            <!-- Сеть -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Network
                </label>
                <div class="bg-gray-100 dark:bg-gray-800 px-4 py-3 rounded-lg">
                    <span x-text="networkLabel" class="text-gray-900 dark:text-white font-medium"></span>
                </div>
            </div>

            <!-- Адрес -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Deposit Address
                </label>
                <div class="relative">
                    <input
                        type="text"
                        x-model="address"
                        readonly
                        class="w-full px-4 py-3 pr-24 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white font-mono text-sm"
                    >
                    <button
                        type="button"
                        x-on:click="copyAddress()"
                        class="absolute right-2 top-1/2 -translate-y-1/2 px-4 py-1.5 bg-cabinet-green hover:bg-cabinet-green/90 text-white rounded-md text-sm font-medium transition-colors"
                    >
                        Copy
                    </button>
                </div>
            </div>

            <!-- Информация -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Important:</h4>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Send only <span x-text="token" class="font-semibold"></span> to this address</li>
                    <li>• Network: <span x-text="networkLabel" class="font-semibold"></span></li>
                    <li>• Minimum: 50 <span x-text="token" class="font-semibold"></span></li>
                    <li>• Funds will be credited after network confirmations</li>
                </ul>
            </div>

            <!-- Кнопка подтверждения отправки -->
{{--            <div x-show="!paymentConfirmed">--}}
{{--                <button--}}
{{--                    type="button"--}}
{{--                    x-on:click="confirmPayment()"--}}
{{--                    class="w-full px-4 py-3 bg-cabinet-orange hover:bg-cabinet-orange/90 text-white rounded-md text-sm font-semibold uppercase transition"--}}
{{--                >--}}
{{--                    I Sent the Funds--}}
{{--                </button>--}}
{{--            </div>--}}

            <!-- Сообщение после подтверждения -->
            <div x-show="paymentConfirmed" class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <p class="text-sm text-gray-700">
                    ✓ Payment confirmation sent! Your funds will be credited after network confirmations.
                </p>
            </div>
        </div>

        <!-- Ошибка -->
        <div x-show="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <p class="text-sm text-red-800 dark:text-red-300" x-text="error"></p>
        </div>
    </div>
</x-cabinet.rightbar>

@push('scripts')
<script>
    function depositUSDT() {
        return {
            step: 1, // 1 = выбор токена, 2 = сумма/сеть, 3 = QR код
            loading: false,
            address: '',
            network: '',
            token: '',
            qrCode: '',
            error: '',
            selectedNetwork: '',
            selectedToken: '',
            amount: '',
            paymentConfirmed: false,
            get networkLabel() {
                if (this.network === 'tron') return 'TRON (TRC-20)';
                if (this.network === 'ethereum') return 'Ethereum (ERC-20)';
                if (this.network === 'bsc') return 'BNB Chain (BEP-20)';
                return (this.network || '').toUpperCase();
            },

            init() {
                console.log('=== DEPOSIT INIT ===');
                // Принудительно сбрасываем состояние при инициализации
                this.resetState();

                // Слушаем событие открытия rightbar
                window.addEventListener('open-rightbar', (e) => {
                    if (e.detail?.name === 'deposit-sidebar') {
                        console.log('=== DEPOSIT RIGHTBAR OPENED ===');
                        this.resetState();
                        console.log('Deposit rightbar opened, state reset, step:', this.step);
                    }
                });
            },

            resetState() {
                this.step = 1;
                this.address = '';
                this.amount = '';
                this.selectedToken = '';
                this.selectedNetwork = '';
                this.paymentConfirmed = false;
                this.error = '';
                this.loading = false;
                console.log('resetState called, step:', this.step, 'selectedToken:', this.selectedToken);
            },

            selectToken(token) {
                this.selectedToken = token;
                this.selectedNetwork = '';
                this.error = '';
                this.step = 2; // Переходим к выбору суммы и сети
                console.log('Token selected:', token, 'moving to step 2');
            },

            async loadDepositAddress() {
                if (!this.selectedToken) {
                    this.error = 'Please select token';
                    return;
                }

                if (!this.selectedNetwork) {
                    this.error = 'Please select network';
                    return;
                }

                this.loading = true;
                this.error = '';

                try {
                    const response = await fetch('{{ route('cabinet.deposit.accept-usdt') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            network: this.selectedNetwork,
                            token: this.selectedToken
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.address = data.address;
                        this.network = data.network;
                        this.token = data.token;
                        this.qrCode = data.qr_code;
                        this.step = 3; // Переходим к показу QR кода
                        console.log('Address loaded, moving to step 3');
                    } else {
                        this.error = data.message || 'Failed to load deposit address';
                    }
                } catch (err) {
                    this.error = 'Network error. Please try again.';
                    console.error('Error loading deposit address:', err);
                } finally {
                    this.loading = false;
                }
            },

            async confirmPayment() {
                this.loading = true;
                this.error = '';

                try {
                    const response = await fetch('{{ route('cabinet.deposit.confirm-payment') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            network: this.network,
                            address: this.address,
                            token: this.token || this.selectedToken
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.paymentConfirmed = true;
                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: {
                                message: 'Payment confirmation sent! Waiting for network confirmations.',
                                type: 'success'
                            }
                        }));
                    } else {
                        this.error = data.message || 'Failed to confirm payment';
                    }
                } catch (err) {
                    this.error = 'Network error. Please try again.';
                    console.error('Error confirming payment:', err);
                } finally {
                    this.loading = false;
                }
            },

            copyAddress() {
                navigator.clipboard.writeText(this.address).then(() => {
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: {
                            message: 'Address copied to clipboard',
                            type: 'success'
                        }
                    }));
                }).catch(err => {
                    console.error('Failed to copy:', err);
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: {
                            message: 'Failed to copy address',
                            type: 'error'
                        }
                    }));
                });
            }
        }
    }
</script>
@endpush

<!-- Withdraw Sidebar -->
<x-cabinet.rightbar name="withdraw-sidebar">
    <x-slot name="title">
        <span x-data="{ get title() {
            const comp = $root.querySelector('[x-ref=withdrawComponent]')?.__x?.$data;
            if (comp?.selectedToken === 'USDT') return 'Withdraw USDT';
            if (comp?.selectedToken === 'USDC') return 'Withdraw USDC';
            return 'Withdraw Crypto';
        }}" x-text="title">Withdraw Crypto</span>
    </x-slot>

    @php
        $availableBalance = auth()->user()->available_balance;
    @endphp

    <div x-data="withdrawFormNew({{ $availableBalance }})" x-ref="withdrawComponent" x-init="init()">
        <!-- Шаг 1: Выбор токена -->
        <div x-show="step === 1" class="space-y-3">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                Select Token
            </label>

            <!-- USDT -->
            <button
                type="button"
                @click="selectToken('USDT')"
                class="w-full flex items-center gap-3 px-4 py-3 bg-[#fcfcfc] border border-[#eeeeee] rounded-lg transition-all hover:border-cabinet-green hover:bg-cabinet-green/5"
            >
                <img src="{{ asset('images/figma/a6c4f721f402307ba991260820140281a188edd1.png') }}" alt="USDT" class="w-8 h-8">
                <div class="flex-1 text-left">
                    <div class="text-sm font-semibold text-gray-500 dark:text-gray-500">USDT</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Tether USD</div>
                </div>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <!-- USDC -->
            <button
                type="button"
                @click="selectToken('USDC')"
                class="w-full flex items-center gap-3 px-4 py-3 bg-[#fcfcfc] border border-[#eeeeee] rounded-lg transition-all hover:border-cabinet-green hover:bg-cabinet-green/5"
            >
                <img src="{{ asset('img/usd-coin-usdc-logo.svg') }}" alt="USDC" class="w-8 h-8">
                <div class="flex-1 text-left">
                    <div class="text-sm font-semibold text-gray-500 dark:text-gray-500">USDC</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">USD Coin</div>
                </div>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                <p class="text-xs text-gray-600">
                    <strong>Important:</strong> Minimum withdrawal is 50 USDT/USDC. Network fee: 2 USDT/USDC.
                </p>
            </div>
        </div>

        <!-- Шаг 2: Сумма, адрес, сеть -->
        <div x-show="step === 2">
            <!-- Кнопка "Назад" -->
            <button
                type="button"
                @click="step = 1; selectedToken = ''; network = 'tron'; $store.userBalance.clearPendingWithdrawal()"
                class="flex items-center gap-2 mb-4 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-900 transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to token selection
            </button>

            <!-- Показываем выбранный токен -->
            <div class="flex items-center gap-3 p-3 bg-[#fcfcfc] border border-[#eeeeee] rounded-lg mb-4">
                <img :src="selectedToken === 'USDT' ? '{{ asset('images/figma/a6c4f721f402307ba991260820140281a188edd1.png') }}' : '{{ asset('img/usd-coin-usdc-logo.svg') }}'" alt="" class="w-6 h-6">
                <span class="text-sm font-medium text-gray-900 dark:text-gray-900" x-text="selectedToken"></span>
            </div>

            <!-- Withdraw Amount -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-[15px] font-semibold text-[#222222]">
                        Withdraw Amount <span class="text-[#e91c2b]">*</span>
                    </label>
                    <span class="text-xs font-medium text-[#01a419]" x-text="'Available: ' + $store.userBalance.displayAvailableBalance.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })">
                        Available: {{ number_format($availableBalance, 2, '.', ',') }}
                    </span>
                </div>
                <div class="relative bg-[#fcfcfc] border border-[#eeeeee] rounded-lg overflow-hidden" style="height: 72px;">
                    <div class="absolute left-3 top-3">
                        <div class="w-5 h-5 rounded-full flex items-center justify-center" :class="selectedToken === 'USDT' ? 'bg-green-100' : 'bg-blue-100'">
                            <span class="text-sm font-bold" :class="selectedToken === 'USDT' ? 'text-green-600' : 'text-blue-600'" x-text="selectedToken === 'USDT' ? '₮' : '$'"></span>
                        </div>
                    </div>
                    <input
                        type="number"
                        step="0.01"
                        min="50"
                        :max="$store.userBalance.displayAvailableBalance"
                        x-model="amount"
                        class="w-full h-full pl-12 pr-4 bg-transparent border-0 text-base text-gray-900 placeholder-gray-600 focus:outline-none focus:ring-0"
                        :placeholder="'Enter amount (min. 50 ' + selectedToken + ')'"
                    >
                    <div class="absolute bottom-2 left-12 text-xs text-[#e91c2b]">
                        2 <span x-text="selectedToken"></span> network fee
                    </div>
                </div>
                <p x-show="errors.amount" x-text="errors.amount" class="mt-1 text-xs text-red-600"></p>
            </div>

            <!-- Network -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Network</label>
                <div class="grid grid-cols-2 gap-2">
                    <!-- Ethereum (USDT & USDC) -->
                    <button
                        type="button"
                        x-show="selectedToken === 'USDT' || selectedToken === 'USDC'"
                        @click="network = 'ethereum'"
                        class="flex flex-col items-center gap-1.5 p-2.5 md:p-3 rounded-md border transition-all"
                        :class="network === 'ethereum' ? 'border-cabinet-green bg-cabinet-green/10' : 'border-gray-300 dark:border-gray-600 hover:border-cabinet-green/50'"
                    >
                        <svg class="w-6 h-6 md:w-7 md:h-7" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 32C24.8366 32 32 24.8366 32 16C32 7.16344 24.8366 0 16 0C7.16344 0 0 7.16344 0 16C0 24.8366 7.16344 32 16 32Z" fill="#627EEA"/>
                            <path d="M16.498 4V12.87L23.995 16.22L16.498 4Z" fill="white" fill-opacity="0.602"/>
                            <path d="M16.498 4L9 16.22L16.498 12.87V4Z" fill="white"/>
                            <path d="M16.498 21.968V27.995L24 17.616L16.498 21.968Z" fill="white" fill-opacity="0.602"/>
                            <path d="M16.498 27.995V21.967L9 17.616L16.498 27.995Z" fill="white"/>
                            <path d="M16.498 20.573L23.995 16.22L16.498 12.872V20.573Z" fill="white" fill-opacity="0.2"/>
                            <path d="M9 16.22L16.498 20.573V12.872L9 16.22Z" fill="white" fill-opacity="0.602"/>
                        </svg>
                        <span class="text-xs font-medium text-gray dark:text-gray-400">Ethereum</span>
                        <span class="text-[13px] text-gray dark:text-gray">ERC-20</span>
                    </button>

                    <!-- TRON (только для USDT) -->
                    <button
                        type="button"
                        x-show="selectedToken === 'USDT'"
                        @click="network = 'tron'"
                        class="flex flex-col items-center gap-1.5 p-2.5 md:p-3 rounded-md border transition-all"
                        :class="network === 'tron' ? 'border-cabinet-green bg-cabinet-green/10' : 'border-gray-300 dark:border-gray-600 hover:border-cabinet-green/50'"
                    >
                        <svg class="w-6 h-6 md:w-7 md:h-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 16">
                            <path fill="#ef0027" d="M8 16c4.4183 0 8 -3.5817 8 -8 0 -4.41828 -3.5817 -8 -8 -8C3.58172 0 0 3.58172 0 8c0 4.4183 3.58172 8 8 8Z"></path>
                            <path fill="#ffffff" d="M10.966 4.95654 3.75 3.62854l3.7975 9.55601 5.2915 -6.447 -1.873 -1.78101ZM10.85 5.54155l1.104 1.0495 -3.019 0.5465 1.915 -1.596Zm-2.571 1.4865 -3.182 -2.63901 5.201 0.95701 -2.019 1.682Zm-0.2265 0.467 -0.519 4.29L4.736 4.74354l3.3165 2.75151Zm0.48 0.2275 3.3435 -0.605 -3.835 4.6715 0.4915 -4.0665Z"></path>
                        </svg>
                        <span class="text-xs font-medium text-gray dark:text-gray-400">TRON</span>
                        <span class="text-[13px] text-gray dark:text-gray">TRC-20</span>
                    </button>

                    <!-- BNB Smart Chain (только для USDC) -->
                    <button
                        type="button"
                        x-show="selectedToken === 'USDC'"
                        @click="network = 'bsc'"
                        class="flex flex-col items-center gap-1.5 p-2.5 md:p-3 rounded-md border transition-all"
                        :class="network === 'bsc' ? 'border-cabinet-green bg-cabinet-green/10' : 'border-gray-300 dark:border-gray-600 hover:border-cabinet-green/50'"
                    >
                        <svg class="w-6 h-6 md:w-7 md:h-7" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 32C24.8366 32 32 24.8366 32 16C32 7.16344 24.8366 0 16 0C7.16344 0 0 7.16344 0 16C0 24.8366 7.16344 32 16 32Z" fill="#F3BA2F"/>
                            <path d="M12.116 14.404L16 10.52L19.886 14.404L22.146 12.144L16 6L9.856 12.144L12.116 14.404ZM6 16L8.26 13.74L10.52 16L8.26 18.26L6 16ZM12.116 17.596L16 21.48L19.886 17.596L22.146 19.856L16 26L9.856 19.856L12.116 17.596ZM21.48 16L23.74 13.74L26 16L23.74 18.26L21.48 16ZM16 18.26L13.74 16L16 13.74L18.26 16L16 18.26Z" fill="white"/>
                        </svg>
                        <span class="text-xs font-medium text-gray dark:text-gray-400">BNB Chain</span>
                        <span class="text-[13px] text-gray dark:text-gray">BEP-20</span>
                    </button>
                </div>
            </div>

            <!-- Receiver Address -->
            <div class="mb-6">
                <label class="block text-[15px] font-semibold text-[#222222] mb-3">
                    Receiver Address <span class="text-[#e91c2b]">*</span>
                </label>
                <input
                    type="text"
                    x-model="receiverAddress"
                    class="w-full px-4 py-3 bg-[#fcfcfc] border border-[#eeeeee] rounded-lg text-base text-gray-900 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-cabinet-orange focus:border-transparent"
                    placeholder="Receiver Address"
                >
                <p x-show="errors.receiver_address" x-text="errors.receiver_address" class="mt-1 text-xs text-red-600"></p>
            </div>

            <!-- Submit Button Step 2 -->
            <button
                type="button"
                @click="sendCode"
                :disabled="loading || !amount || !receiverAddress || !network"
                class="w-full py-3 bg-[#ff451c] hover:bg-[#ff451c]/90 text-white rounded-md text-sm font-extrabold uppercase transition disabled:opacity-50"
            >
                <span x-show="!loading">Create a withdrawal</span>
                <span x-show="loading">Sending code...</span>
            </button>
        </div>

        <!-- Шаг 3: Verification Code -->
        <div x-show="step === 3">
            <!-- Info -->
            <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                <p class="text-sm text-gray-700">
                    A 6-digit verification code has been sent to <strong class="text-cabinet-green">{{ $user->email }}</strong>
                </p>
            </div>

            <!-- Verification Code -->
            <div class="mb-4">
                <label class="block text-[17px] font-semibold text-[#222222] mb-3">
                    Verification Code <span class="text-white">*</span>
                </label>
                <div class="relative">
                    <input
                        type="text"
                        x-model="code"
                        maxlength="6"
                        pattern="[0-9]*"
                        inputmode="numeric"
                        @input="code = code.replace(/[^0-9]/g, '')"
                        class="text-base w-full px-4 py-3 pr-24 bg-[#fcfcfc] border border-[#eeeeee] rounded-lg text-center  tracking-widest focus:outline-none focus:ring-2 focus:ring-cabinet-orange focus:border-transparent"
                        placeholder="000000"
                    >
                    <button
                        type="button"
                        @click="async () => {
                            try {
                                const text = await navigator.clipboard.readText();
                                const digits = text.replace(/[^0-9]/g, '').slice(0, 6);
                                if (digits.length === 6) {
                                    code = digits;
                                    window.dispatchEvent(new CustomEvent('show-toast', {
                                        detail: { message: 'Code inserted from clipboard', type: 'success' }
                                    }));
                                } else {
                                    window.dispatchEvent(new CustomEvent('show-toast', {
                                        detail: { message: 'Invalid code format in clipboard', type: 'error' }
                                    }));
                                }
                            } catch (err) {
                                window.dispatchEvent(new CustomEvent('show-toast', {
                                    detail: { message: 'Failed to read clipboard', type: 'error' }
                                }));
                            }
                        }"
                        class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1.5 bg-cabinet-green hover:bg-cabinet-green/90 text-white text-xs font-semibold rounded transition-colors"
                    >
                        Paste
                    </button>
                </div>
                <p x-show="errors.code" x-text="errors.code" class="mt-1 text-xs text-red-600"></p>
            </div>

            <!-- Status Message -->
            <p x-show="statusMessage" x-text="statusMessage" :class="codeVerified ? 'text-green-600' : 'text-gray-600'" class="text-sm mb-4 font-medium"></p>

            <!-- Resend Code -->
            <div class="mb-6 text-center">
                <button
                    type="button"
                    @click="resendCode"
                    :disabled="resendLoading || resendCooldown > 0"
                    class="text-[15px] font-semibold text-[#ff451c] hover:text-[#ff451c]/80 disabled:opacity-50"
                >
                    <span x-show="!resendLoading && resendCooldown === 0">Resend Code</span>
                    <span x-show="resendLoading">Sending...</span>
                    <span x-show="resendCooldown > 0" x-text="'Resend in ' + resendCooldown + 's'"></span>
                </button>
            </div>

            <!-- Submit Button Step 2 -->
            <button
                type="button"
                @click="confirmWithdraw"
                :disabled="loading || code.length !== 6"
                class="w-full py-3 bg-[#ff451c] hover:bg-[#ff451c]/90 text-white rounded-md text-sm font-extrabold uppercase transition disabled:opacity-50"
            >
                <span x-show="!loading">Confirm</span>
                <span x-show="loading">Processing...</span>
            </button>
        </div>
    </div>
</x-cabinet.rightbar>

@push('scripts')
<script>
    function withdrawFormNew(availableBalance) {
        return {
            step: 1, // 1 = выбор токена, 2 = сумма/адрес/сеть, 3 = код подтверждения
            selectedToken: '',
            network: 'tron',
            amount: '',
            receiverAddress: '',
            code: '',
            errors: {},
            loading: false,
            resendLoading: false,
            availableBalance: availableBalance,
            codeVerified: false,
            statusMessage: '',
            resendCooldown: 0,
            cooldownInterval: null,

            init() {
                console.log('=== WITHDRAW INIT ===');
                // Сброс формы при открытии rightbar
                window.addEventListener('open-rightbar', (e) => {
                    if (e.detail.name === 'withdraw-sidebar') {
                        console.log('=== WITHDRAW RIGHTBAR OPENED ===');
                        this.reset();
                    }
                });
            },

            reset() {
                this.step = 1;
                this.selectedToken = '';
                this.network = 'tron';
                this.amount = '';
                this.receiverAddress = '';
                this.code = '';
                this.errors = {};
                this.loading = false;
                this.resendLoading = false;
                this.codeVerified = false;
                this.statusMessage = '';
                this.resendCooldown = 0;
                if (this.cooldownInterval) {
                    clearInterval(this.cooldownInterval);
                    this.cooldownInterval = null;
                }

                // Do not clear client-side pending reservation here to preserve UI deduction across confirmation
                // Alpine.store('userBalance').clearPendingWithdrawal();

                console.log('Withdraw reset, step:', this.step);
            },

            selectToken(token) {
                this.selectedToken = token;
                this.network = 'tron'; // По умолчанию TRON
                this.step = 2; // Переходим к сумме и адресу
                console.log('Token selected:', token, 'moving to step 2');
            },

            async sendCode() {
                this.errors = {};
                this.loading = true;

                try {
                    const response = await fetch('{{ route('cabinet.withdraw.request') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            amount: parseFloat(this.amount),
                            receiver_address: this.receiverAddress,
                            network: this.network,
                            token: this.selectedToken
                        })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        if (response.status === 422 && data.errors) {
                            this.errors = {};
                            Object.keys(data.errors).forEach(key => {
                                this.errors[key] = data.errors[key][0];
                            });
                        } else {
                            window.dispatchEvent(new CustomEvent('show-toast', {
                                detail: {
                                    message: data.message || 'An error occurred',
                                    type: 'error'
                                }
                            }));
                        }
                        return;
                    }

                    // Успешно отправлен код
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: {
                            message: data.message,
                            type: 'success'
                        }
                    }));

                    // Save pending withdrawal amount to store (client-side only)
                    Alpine.store('userBalance').setPendingWithdrawal(this.amount);

                    this.step = 3; // Переходим к коду подтверждения
                    this.startResendCooldown();
                    console.log('Code sent, moving to step 3');
                } catch (err) {
                    console.error('Error:', err);
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: {
                            message: 'Network error. Please try again.',
                            type: 'error'
                        }
                    }));
                } finally {
                    this.loading = false;
                }
            },

            async confirmWithdraw() {
                this.statusMessage = '';
                this.loading = true;

                try {
                    const response = await fetch('{{ route('cabinet.withdraw.confirm') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            code: this.code
                        })
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        this.codeVerified = true;
                        this.statusMessage = '✓ ' + data.message;

                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: {
                                message: data.message,
                                type: 'success'
                            }
                        }));

                        // Закрываем rightbar и сбрасываем форму
                        setTimeout(() => {
                            this.$dispatch('close-rightbar');
                            this.reset();
                            // Sync balances to align client reservation with server-side pending
                            if (window.Alpine && Alpine.store('userBalance')) {
                                Alpine.store('userBalance').sync();
                            }
                            window.location.reload();
                        }, 1500);
                    } else {
                        this.statusMessage = data.message || 'Invalid confirmation code';

                        if (response.status !== 422) {
                            window.dispatchEvent(new CustomEvent('show-toast', {
                                detail: {
                                    message: data.message || 'An error occurred',
                                    type: 'error'
                                }
                            }));
                        }
                    }
                } catch (err) {
                    console.error('Error:', err);
                    this.statusMessage = 'Network error. Please try again.';
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: {
                            message: 'Network error. Please try again.',
                            type: 'error'
                        }
                    }));
                } finally {
                    this.loading = false;
                }
            },

            async resendCode() {
                if (this.resendCooldown > 0) return;

                this.resendLoading = true;
                this.statusMessage = '';

                try {
                    const response = await fetch('{{ route('cabinet.withdraw.resend-code') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: {
                            message: data.message,
                            type: data.success ? 'success' : 'error'
                        }
                    }));

                    if (data.success) {
                        this.code = '';
                        this.startResendCooldown();
                    }
                } catch (err) {
                    console.error('Error:', err);
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: {
                            message: 'Network error. Please try again.',
                            type: 'error'
                        }
                    }));
                } finally {
                    this.resendLoading = false;
                }
            },

            startResendCooldown() {
                this.resendCooldown = 60;

                if (this.cooldownInterval) {
                    clearInterval(this.cooldownInterval);
                }

                this.cooldownInterval = setInterval(() => {
                    this.resendCooldown--;
                    if (this.resendCooldown <= 0) {
                        clearInterval(this.cooldownInterval);
                        this.cooldownInterval = null;
                    }
                }, 1000);
            }
        }
    }
</script>
@endpush

<!-- Tier Information Sidebars -->
@php
    $allTiers = \App\Models\Tier::with('investmentPools')->orderBy('level')->get();
@endphp

@foreach($allTiers as $tier)
    @php
        $nextTier = \App\Models\Tier::where('level', '>', $tier->level)->orderBy('level')->first();
        $maxBalance = $nextTier ? $nextTier->min_balance - 1 : null;

        if ($user->account_type === 'islamic') {
            $pools = $tier->islamicInvestmentPools()
                ->orderBy('duration_days')
                ->get()
                ->map(function ($pool) use ($tier) {
                    return (object) [
                        'name' => $tier->name . ' ' . $pool->duration_days . ' days',
                        'days' => $pool->duration_days,
                        'percentage_label' => $pool->min_percentage . '% - ' . $pool->max_percentage . '%',
                        'percentage' => $pool->min_percentage,
                    ];
                });
        } else {
            $pools = $tier->investmentPools()
                ->where('is_active', true)
                ->orderBy('order')
                ->get()
                ->map(function ($pool) {
                    $pool->percentage_label = rtrim(rtrim(number_format($pool->percentage, 2), '0'), '.') . '%';
                    return $pool;
                });
        }
    @endphp
    <x-cabinet.rightbar name="tier-{{ $tier->id }}">
        <x-slot name="title">
            <div class="flex items-center justify-between w-full">
                <span>{{ $tier->name }}</span>
            </div>
        </x-slot>

        <div class="space-y-4">
            <!-- Balance Range -->
            <div class="bg-white rounded-lg p-4 border border-gray-200">
                <p class="text-xs font-medium text-gray-500 mb-1">Balance Range</p>
                <p class="text-lg font-bold text-gray-900">
                    ${{ number_format($tier->min_balance, 0, '.', ',') }}
                    @if($maxBalance)
                        - ${{ number_format($maxBalance, 0, '.', ',') }}
                    @else
                        +
                    @endif
                    USD
                </p>
            </div>

            <!-- Investment Pools -->
            @if($pools->isNotEmpty())
                <div class="space-y-3">
                    @foreach($pools as $pool)
                        @php
                            // Normalize pool to object
                            $poolObj = is_array($pool) ? (object)$pool : $pool;

                            // Convert hex to rgba with 10% opacity for background
                            $hex = $tier->color ?? '#f6bd0e';
                            $hex = ltrim($hex, '#');
                            $r = hexdec(substr($hex, 0, 2));
                            $g = hexdec(substr($hex, 2, 2));
                            $b = hexdec(substr($hex, 4, 2));
                            $bgColor = "rgba($r, $g, $b, 0.1)";

                            // Light colors for inner diamond from Figma
                            $lightColorMap = [
                                '#f6bd0e' => '#fff9e7', // Starter (yellow)
                                '#ff613e' => '#ffdad2', // Bronze (orange)
                                '#f70808' => '#ffdad2', // Silver (red)
                                '#e10495' => '#fce6f5', // Gold (pink)
                                '#c000ca' => 'rgba(255, 255, 255, 0.7)', // Platinum (purple)
                                '#2f7dea' => 'rgba(255, 255, 255, 0.7)', // Titanium (blue)
                                '#1393af' => 'rgba(255, 255, 255, 0.7)', // Diamond (cyan)
                                '#29bdb4' => 'rgba(255, 255, 255, 0.7)', // Elite Diamond (teal)
                                '#82ca8a' => 'rgba(255, 255, 255, 0.7)', // Crown Elite (green)
                                '#caca33' => 'rgba(255, 255, 255, 0.7)', // Royal Legacy (yellow-green)
                            ];
                            $lightColor = $lightColorMap[$tier->color] ?? 'rgba(255, 255, 255, 0.7)';
                        @endphp
                        <div class="relative rounded-md border" style="background-color: {{ $bgColor }}; border-color: {{ $tier->color ?? '#f6bd0e' }}; height: 105px;">
                            <!-- Left colored stripe -->
                            <div class="absolute left-0 top-0 bottom-0 w-2 rounded-tl-md rounded-bl-md" style="background-color: {{ $tier->color ?? '#f6bd0e' }}"></div>

                            <!-- Diamond decoration -->
                            <div class="diam-dec absolute -left-2.5 top-9">
                                <!-- Outer diamond -->
                                <div class="w-[26px] h-[25px] flex items-center justify-center">
                                    <div class="w-[26px] h-[25px] rotate-45" style="background-color: {{ $tier->color ?? '#f6bd0e' }}"></div>
                                </div>
                                <!-- Inner diamond -->
                                <div class="absolute top-[3px] left-[3px] w-[19px] h-[19px] flex items-center justify-center">
                                    <div class="w-[19px] h-[19px] rotate-45" style="background-color: {{ $lightColor }}"></div>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="absolute left-[37px] right-0 top-0 bottom-0 flex flex-col justify-between py-4 pr-4">
                                <!-- Top row: Title and Days -->
                                <div class="flex items-center justify-between">
                                    <h3 class="text-base font-extrabold text-[#222222]">{{ $poolObj->name }}</h3>
                                    <span class="text-sm font-medium text-[#444444]">{{ $poolObj->days }} Days</span>
                                </div>

                                <!-- Divider line -->
                                <div class="border-t border-dashed border-[#cccccc]"></div>

                                <!-- Bottom row: ROI label and percentage -->
                                <div class="flex items-center justify-between">
                                    <span class="text-base font-medium text-[#444444]">Return On Investment</span>
                                    <span class="text-base font-extrabold text-[#01a419]">{{ $poolObj->percentage_label ?? ($poolObj->percentage . '%') }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <p class="text-sm">No investment pools available for this tier</p>
                </div>
            @endif
        </div>
    </x-cabinet.rightbar>
@endforeach

<x-toast />

<!-- Universal Modal -->
<div id="universal-modal"
     x-data="{
         open: false,
         title: '',
         message: '',
         confirmText: 'OK',
         init() {
             window.addEventListener('open-modal', (e) => {
                 this.title = e.detail.title || 'Information';
                 this.message = e.detail.message || '';
                 this.confirmText = e.detail.confirmText || 'OK';
                 this.open = true;
             });
         }
     }"
     x-show="open"
     x-cloak
     @keydown.escape.window="open = false"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50 transition-opacity" @click="open = false"></div>

    <!-- Modal Dialog -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div @click.stop
             class="relative bg-white rounded-lg shadow-xl max-w-lg w-full p-6"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

            <!-- Close Button -->
            <button @click="open = false"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- Title -->
            <h3 class="text-xl font-bold text-gray-900 mb-4" x-text="title"></h3>

            <!-- Content -->
            <div class="text-gray-700 mb-6" x-html="message"></div>

            <!-- Actions -->
            <div class="flex justify-end gap-3">
                <button @click="open = false"
                        class="px-6 py-2.5 bg-cabinet-green text-white rounded-lg font-semibold hover:bg-cabinet-green/90 transition-colors"
                        x-text="confirmText">
                </button>
            </div>
        </div>
    </div>
</div>

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
