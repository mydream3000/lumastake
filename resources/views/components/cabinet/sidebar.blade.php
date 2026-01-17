@php
    $currentRoute = request()->route()->getName();
    $transactionRoutes = ['cabinet.transactions.deposits', 'cabinet.transactions.withdraw'];
    $earningsRoutes = ['cabinet.earnings.profit', 'cabinet.earnings.rewards'];
    $isTransactionActive = in_array($currentRoute, $transactionRoutes);
    $isEarningsActive = in_array($currentRoute, $earningsRoutes);
@endphp

<div class="hidden lg:flex flex-col w-[346px] bg-cabinet-dark" style="min-height: max(100vh, 100%)">
    {{-- Logo --}}
    <div class="flex items-center justify-center pt-12 pb-11">
        <a href="{{ route('cabinet.dashboard') }}">
            <img src="{{ asset('images/sidebar/logo-white.png') }}" alt="Lumastake" class="h-11 w-auto">
        </a>
    </div>

    {{-- Navigation --}}
    <div class="flex flex-col flex-1 overflow-y-auto">
        <nav class="flex-1 px-[34px] space-y-2">
            {{-- Dashboard --}}
            <a href="{{ route('cabinet.dashboard') }}"
               class="flex items-center gap-4 px-[37px] py-3 font-manrope font-medium text-[25px] leading-[34px] rounded-[33px] transition-colors
                      {{ $currentRoute === 'cabinet.dashboard' ? 'bg-white/20 text-cabinet-orange' : 'text-cabinet-text-dark hover:bg-white/10' }}">
                <img src="{{ asset('images/sidebar/icon-dashboard.png') }}" alt="Dashboard" class="w-6 h-6 brightness-0 invert">
                <span>Dashboard</span>
            </a>

            {{-- Pools --}}
            <a href="{{ route('cabinet.pools.index') }}"
               class="flex items-center gap-[13px] px-6 py-3 font-manrope font-medium text-[25px] leading-[34px] rounded-[33px] transition-colors
                      {{ $currentRoute === 'cabinet.pools.index' ? 'bg-white/20 text-cabinet-orange' : 'text-cabinet-text-dark hover:bg-white/10' }}">
                <img src="{{ asset('images/sidebar/icon-pools.png') }}" alt="Pools" class="w-[37px] h-[37px] brightness-0 invert">
                <span>Pools</span>
            </a>

            {{-- Stakings --}}
            <a href="{{ route('cabinet.staking.index') }}"
               class="flex items-center gap-[16px] px-[23px] py-3 text-cabinet-text-dark font-manrope font-medium text-[25px] leading-[34px] rounded-[33px] transition-colors
                      {{ $currentRoute === 'cabinet.staking.index' ? 'bg-white/20 text-cabinet-orange' : 'hover:bg-white/10' }}">
                <img src="{{ asset('images/sidebar/icon-staking.png') }}" alt="Stakings" class="w-[31px] h-[31px] brightness-0 invert">
                <span>Stakings</span>
            </a>

            {{-- Stake History --}}
            <a href="{{ route('cabinet.history') }}"
               class="flex items-center gap-[16px] px-5 py-3 text-cabinet-text-dark font-manrope font-medium text-[25px] leading-[34px] rounded-[33px] transition-colors
                      {{ $currentRoute === 'cabinet.history' ? 'bg-white/20 text-cabinet-orange' : 'hover:bg-white/10' }}">
                <img src="{{ asset('images/sidebar/icon-stake-history.png') }}" alt="Stake History" class="w-8 h-8 brightness-0 invert">
                <span>Stake History</span>
            </a>

            {{-- Transactions --}}
            <div x-data="{ open: {{ $isTransactionActive ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="flex items-center justify-between w-full gap-[19px] px-[23px] py-3 text-cabinet-text-dark font-manrope font-medium text-[25px] leading-[34px] rounded-[33px] transition-colors
                               {{ $isTransactionActive ? 'bg-white/20 text-cabinet-orange' : 'hover:bg-white/10' }}">
                    <div class="flex items-center gap-[19px]">
                        <img src="{{ asset('images/sidebar/icon-transaction.png') }}" alt="Transactions" class="w-[29px] h-[29px] brightness-0 invert">
                        <span>Transactions</span>
                    </div>
                    <img src="{{ asset('images/sidebar/arrow-down.svg') }}" alt="" class="w-[19px] h-[19px] transition-transform" :class="{'rotate-180': open}">
                </button>
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="pl-[60px] mt-2 space-y-2">
                    <a href="{{ route('cabinet.transactions.deposits') }}" class="block py-2 text-cabinet-text-dark font-manrope font-medium text-[20px] hover:text-cabinet-orange transition-colors {{ $currentRoute === 'cabinet.transactions.deposits' ? 'text-cabinet-orange' : '' }}">Deposits</a>
                    <a href="{{ route('cabinet.transactions.withdraw') }}" class="block py-2 text-cabinet-text-dark font-manrope font-medium text-[20px] hover:text-cabinet-orange transition-colors {{ $currentRoute === 'cabinet.transactions.withdraw' ? 'text-cabinet-orange' : '' }}">Withdraw</a>
                </div>
            </div>

            {{-- Earnings --}}
            <div x-data="{ open: {{ $isEarningsActive ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="flex items-center justify-between w-full gap-[15px] px-5 py-3 text-cabinet-text-dark font-manrope font-medium text-[25px] leading-[34px] rounded-[33px] transition-colors
                               {{ $isEarningsActive ? 'bg-white/20 text-cabinet-orange' : 'hover:bg-white/10' }}">
                    <div class="flex items-center gap-[15px]">
                        <img src="{{ asset('images/sidebar/icon-earning.png') }}" alt="Earnings" class="w-[37px] h-[37px] brightness-0 invert">
                        <span>Earnings</span>
                    </div>
                    <img src="{{ asset('images/sidebar/arrow-down.svg') }}" alt="" class="w-[19px] h-[19px] transition-transform" :class="{'rotate-180': open}">
                </button>
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="pl-[60px] mt-2 space-y-2">
                    <a href="{{ route('cabinet.earnings.profit') }}" class="block py-2 text-cabinet-text-dark font-manrope font-medium text-[20px] hover:text-cabinet-orange transition-colors {{ $currentRoute === 'cabinet.earnings.profit' ? 'text-cabinet-orange' : '' }}">Profits</a>
                    <a href="{{ route('cabinet.earnings.rewards') }}" class="block py-2 text-cabinet-text-dark font-manrope font-medium text-[20px] hover:text-cabinet-orange transition-colors {{ $currentRoute === 'cabinet.earnings.rewards' ? 'text-cabinet-orange' : '' }}">Rewards</a>
                </div>
            </div>

            {{-- Rewards --}}
            <a href="{{ route('cabinet.rewards') }}"
               class="flex items-center gap-[19px] px-[25px] py-3 text-cabinet-text-dark font-manrope font-medium text-[25px] leading-[34px] rounded-[33px] transition-colors
                      {{ $currentRoute === 'cabinet.rewards' ? 'bg-white/20 text-cabinet-orange' : 'hover:bg-white/10' }}">
                <img src="{{ asset('images/sidebar/icon-reward.png') }}" alt="Rewards" class="w-[27px] h-[27px] brightness-0 invert">
                <span>Rewards</span>
            </a>

            {{-- Profile --}}
            <a href="{{ route('cabinet.profile.show') }}"
               class="flex items-center gap-[17px] px-[25px] py-3 font-manrope font-medium text-[25px] leading-[34px] rounded-[33px] transition-colors
                      {{ $currentRoute === 'cabinet.profile.show' || $currentRoute === 'cabinet.profile.edit' ? 'bg-white/20 text-cabinet-orange' : 'text-cabinet-text-dark hover:bg-white/10' }}">
                <img src="{{ asset('images/sidebar/icon-profile.png') }}" alt="Profile" class="w-[26px] h-[26px] brightness-0 invert">
                <span>Profile</span>
            </a>
        </nav>
    </div>
    <!-- Bottom decorative image (desktop only) -->
    <div class="hidden md:block px-[12px] pb-6 select-none">
        <img src="{{ asset('img/dashboard-img/growth_1.png') }}" alt="" class="w-full h-auto pointer-events-none">
    </div>
</div>
