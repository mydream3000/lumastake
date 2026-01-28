@php
    $currentRoute = request()->route()->getName();
    $transactionRoutes = ['cabinet.transactions.deposits', 'cabinet.transactions.withdraw'];
    $earningsRoutes = ['cabinet.earnings.profit', 'cabinet.earnings.rewards'];
    $isTransactionActive = in_array($currentRoute, $transactionRoutes);
    $isEarningsActive = in_array($currentRoute, $earningsRoutes);
@endphp

<div class="hidden lg:flex flex-col w-[346px] bg-gradient-to-b from-cabinet-sidebar-start from-[10%] to-cabinet-sidebar-end pr-8" style="min-height: max(100vh, 100%)">
    {{-- Logo --}}
    <div class="flex items-center justify-center pt-12 pb-11">
        <a href="{{ route('cabinet.dashboard') }}">
            <img src="{{ asset('images/sidebar/logo-white.png') }}" alt="Lumastake" class="h-11 w-auto">
        </a>
    </div>

    {{-- Navigation --}}
    <div class="flex flex-col flex-1 overflow-y-auto">
        <nav class="flex-1 px-4 space-y-2">
            {{-- Dashboard --}}
            <a href="{{ route('cabinet.dashboard') }}"
               class="flex items-center gap-4 px-8 py-3 font-manrope font-medium text-[22px] leading-tight rounded-xl transition-colors
                      {{ request()->routeIs('cabinet.dashboard') ? 'sidebar-active' : 'text-white hover:bg-white/10' }}">
                <div class="w-8 flex justify-center">
                    <img src="{{ asset('images/sidebar/icon-dashboard.png') }}" alt="Dashboard" class="w-6 h-6">
                </div>
                <span>Dashboard</span>
            </a>

            {{-- Pools --}}
            <a href="{{ route('cabinet.pools.index') }}"
               class="flex items-center gap-4 px-8 py-3 font-manrope font-medium text-[22px] leading-tight rounded-xl transition-colors
                      {{ request()->routeIs('cabinet.pools.*') ? 'sidebar-active' : 'text-white hover:bg-white/10' }}">
                <div class="w-8 flex justify-center">
                    <img src="{{ asset('images/sidebar/icon-pools.png') }}" alt="Pools" class="w-8 h-8">
                </div>
                <span>Pools</span>
            </a>

            {{-- Stakings --}}
            <a href="{{ route('cabinet.staking.index') }}"
               class="flex items-center gap-4 px-8 py-3 font-manrope font-medium text-[22px] leading-tight rounded-xl transition-colors
                      {{ request()->routeIs('cabinet.staking.*') ? 'sidebar-active' : 'text-white hover:bg-white/10' }}">
                <div class="w-8 flex justify-center">
                    <img src="{{ asset('images/sidebar/icon-staking.png') }}" alt="Stakings" class="w-7 h-7">
                </div>
                <span>Stakings</span>
            </a>

            {{-- Stake History --}}
            <a href="{{ route('cabinet.history') }}"
               class="flex items-center gap-4 px-8 py-3 font-manrope font-medium text-[22px] leading-tight rounded-xl transition-colors
                      {{ request()->routeIs('cabinet.history') ? 'sidebar-active' : 'text-white hover:bg-white/10' }}">
                <div class="w-8 flex justify-center">
                    <img src="{{ asset('images/sidebar/icon-stake-history.png') }}" alt="Stake History" class="w-7 h-7">
                </div>
                <span>Stake History</span>
            </a>

            {{-- Transactions --}}
            <div x-data="{ open: {{ $isTransactionActive ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="flex items-center justify-between w-full gap-4 px-8 py-3 font-manrope font-medium text-[22px] leading-tight rounded-xl transition-colors
                               {{ $isTransactionActive ? 'sidebar-active' : 'text-white hover:bg-white/10' }}">
                    <div class="flex items-center gap-4">
                        <div class="w-8 flex justify-center">
                            <img src="{{ asset('images/sidebar/icon-transaction.png') }}" alt="Transactions" class="w-7 h-7">
                        </div>
                        <span>Transaction</span>
                    </div>
                    <svg class="w-5 h-5 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open"
                     class="pl-[72px] mt-1 space-y-1">
                    <a href="{{ route('cabinet.transactions.deposits') }}" class="block py-2 text-white/70 font-manrope font-medium text-[18px] hover:text-white transition-colors {{ $currentRoute === 'cabinet.transactions.deposits' ? 'text-cabinet-lime' : '' }}">Deposits</a>
                    <a href="{{ route('cabinet.transactions.withdraw') }}" class="block py-2 text-white/70 font-manrope font-medium text-[18px] hover:text-white transition-colors {{ $currentRoute === 'cabinet.transactions.withdraw' ? 'text-cabinet-lime' : '' }}">Withdraw</a>
                </div>
            </div>

            {{-- Earnings --}}
            <div x-data="{ open: {{ $isEarningsActive ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="flex items-center justify-between w-full gap-4 px-8 py-3 font-manrope font-medium text-[22px] leading-tight rounded-xl transition-colors
                               {{ $isEarningsActive ? 'sidebar-active' : 'text-white hover:bg-white/10' }}">
                    <div class="flex items-center gap-4">
                        <div class="w-8 flex justify-center">
                            <img src="{{ asset('images/sidebar/icon-earning.png') }}" alt="Earnings" class="w-8 h-8">
                        </div>
                        <span>Earning</span>
                    </div>
                    <svg class="w-5 h-5 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open"
                     class="pl-[72px] mt-1 space-y-1">
                    <a href="{{ route('cabinet.earnings.profit') }}" class="block py-2 text-white/70 font-manrope font-medium text-[18px] hover:text-white transition-colors {{ $currentRoute === 'cabinet.earnings.profit' ? 'text-cabinet-lime' : '' }}">Profits</a>
                    <a href="{{ route('cabinet.earnings.rewards') }}" class="block py-2 text-white/70 font-manrope font-medium text-[18px] hover:text-white transition-colors {{ $currentRoute === 'cabinet.earnings.rewards' ? 'text-cabinet-lime' : '' }}">Rewards</a>
                </div>
            </div>

            {{-- Rewards --}}
            <a href="{{ route('cabinet.rewards') }}"
               class="flex items-center gap-4 px-8 py-3 font-manrope font-medium text-[22px] leading-tight rounded-xl transition-colors
                      {{ request()->routeIs('cabinet.rewards') ? 'sidebar-active-reward bg-white/15 text-cabinet-lime' : 'text-white hover:bg-white/10' }}">
                <div class="w-8 flex justify-center">
                    <img src="{{ asset('images/sidebar/icon-reward.png') }}" alt="Rewards" class="w-7 h-7">
                </div>
                <span>Reward</span>
            </a>

            {{-- Profile --}}
            <a href="{{ route('cabinet.profile.show') }}"
               class="flex items-center gap-4 px-8 py-3 font-manrope font-medium text-[22px] leading-tight rounded-xl transition-colors
                      {{ request()->routeIs('cabinet.profile.*') ? 'sidebar-active' : 'text-white hover:bg-white/10' }}">
                <div class="w-8 flex justify-center">
                    <img src="{{ asset('images/sidebar/icon-profile.png') }}" alt="Profile" class="w-6 h-6">
                </div>
                <span>Profile</span>
            </a>
        </nav>
    </div>
    <!-- Bottom decorative image (desktop only) -->
    <div class="hidden md:block px-[12px] select-none">
        <img src="{{ asset('img/dashboard-img/growth_1.png') }}" alt="" class="w-full h-auto pointer-events-none">
    </div>
</div>
