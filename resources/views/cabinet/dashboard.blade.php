<x-cabinet-layout>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column (2/3) --}}
        <div class="lg:col-span-2 flex flex-col gap-6">

            {{-- Top Row: Balance & Amount --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @php
                    $totalStaked = $user->stakingDeposits()->where('status', 'active')->sum('amount');
                @endphp

                {{-- Balance Card --}}
                <div class="card p-6">
                    <h3 class="font-semibold text-lg text-cabinet-text-grey mb-2">Balance</h3>
                    <p class="font-manrope font-bold text-4xl text-cabinet-text-dark" x-text="$store.userBalance.availableBalance.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' USD'">
                        {{ number_format($user->available_balance, 2) }} USD
                    </p>
                </div>

                {{-- Amount Staked Card --}}
                <div class="card p-6">
                    <h3 class="font-semibold text-lg text-cabinet-text-grey mb-2">Amount Staked</h3>
                    <p class="font-manrope font-bold text-4xl text-cabinet-text-dark">
                        {{ number_format($totalStaked, 2) }} USD
                    </p>
                </div>
            </div>

            {{-- Investment Pools --}}
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-cabinet-text-dark">Investment Pools</h2>
                </div>
                <div class="js-pools-list" data-url="{{ route('cabinet.pools.data') }}" data-balance="{{ $user->available_balance }}"></div>
            </div>

            {{-- Referral Link --}}
            <div class="card p-6">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                    <div class="flex-1 w-full">
                        <h2 class="text-xl font-bold text-cabinet-text-dark mb-2">Referral Link</h2>
                        <p class="text-base text-cabinet-text-grey mb-3">Earn the Profits</p>
                        <div class="flex items-center gap-3 w-full">
                            <input type="text" readonly value="{{ url('/register?ref=' . $user->referral_code) }}" id="referral-link" class="flex-1 bg-cabinet-dark border border-cabinet-grey text-cabinet-text-dark rounded px-4 py-2.5 text-sm">
                            <button onclick="copyReferralLink()" class="bg-cabinet-blue text-white px-6 py-2.5 rounded font-semibold text-sm hover:opacity-90">
                                Copy
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column (1/3): Profit & Calculator --}}
        <div class="lg:col-span-1">
            <div class="card p-6 sticky top-6">

                {{-- Profit Section --}}
                <div class="mb-8 relative">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-cabinet-text-dark">Profit</h3>
                        <select id="profit-period" class="bg-cabinet-dark border border-cabinet-grey text-cabinet-text-grey text-xs rounded-md px-2 py-1 outline-none">
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                        </select>
                    </div>

                    {{-- Chart Container --}}
                    <div class="relative w-[200px] h-[200px] mx-auto">
                        <canvas id="profit-donut-chart"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <p class="text-cabinet-text-grey text-base font-medium mb-0">Total</p>
                            <p class="text-cabinet-text-dark text-xl font-semibold" id="total-profit">${{ number_format($profitChartData['completed'] + $profitChartData['expected'], 1) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Calculate Profit Section --}}
                <div class="pt-6 border-t border-cabinet-grey">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-cabinet-text-dark">Calculate Profit</h3>
                        <div class="flex items-center gap-2 border border-cabinet-lime bg-cabinet-lime/10 px-3 py-1 rounded-md">
                            <span id="calc-tier-name" class="text-sm font-bold text-cabinet-lime"></span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        {{-- Pool Selection --}}
                        <div>
                            <label class="block text-sm font-semibold text-cabinet-text-grey mb-2">Select Pool</label>
                            <div class="relative">
                                <select id="calc-pool-duration" class="w-full bg-cabinet-dark border border-cabinet-grey text-cabinet-text-dark rounded-lg px-4 py-3 text-sm appearance-none outline-none"></select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none">
                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Amount Input --}}
                        <div>
                            <label class="block text-sm font-semibold text-cabinet-text-grey mb-2">Amount</label>
                            <div class="relative">
                                <input type="number" id="calc-amount" min="0" step="100" placeholder="Enter Amount" class="w-full bg-cabinet-dark border border-cabinet-grey text-cabinet-text-dark rounded-lg px-4 py-3 text-sm outline-none">
                            </div>
                        </div>

                        {{-- Calculate Button --}}
                        <button id="calc-button" class="w-full bg-cabinet-blue text-white py-3 rounded-lg font-bold text-base hover:opacity-90 transition-opacity mt-4">
                            Calculate
                        </button>

                        {{-- Result --}}
                        <div class="pt-4 text-left">
                            <p class="text-lg font-semibold text-cabinet-text-dark inline-block mr-2">Expected Profit :</p>
                            <p id="calc-result" class="text-lg font-bold text-cabinet-lime block mt-1"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart Logic and Calculator Logic remains the same
    </script>
    @endpush
</x-cabinet-layout>
