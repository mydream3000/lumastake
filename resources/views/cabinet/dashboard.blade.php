<x-cabinet-layout>
    {{-- Tiers Bar --}}
    @php
        $tiersForDisplay = \App\Models\Tier::orderBy('level')->get();
    @endphp
    <div class="card px-6 py-4 mb-6 overflow-x-auto">
        <div class="flex items-center gap-2 min-w-max">
            @foreach($tiersForDisplay as $index => $tier)
                @php
                    $isCurrent = $currentTier && $currentTier->id === $tier->id;
                    $isCompleted = $currentTier && $currentTier->level > $tier->level;
                @endphp
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        x-on:click="$dispatch('open-rightbar', {name: 'tier-{{ $tier->id }}'})"
                        class="flex items-center gap-1.5 transition-opacity hover:opacity-80 {{ $isCurrent ? 'text-cabinet-blue font-bold' : ($isCompleted ? 'text-cabinet-text-main' : 'text-gray-400') }}"
                    >
                        {{ $tier->name }}
                        @if($isCompleted)
                            <svg class="w-4 h-4 text-cabinet-lime" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        @elseif($isCurrent)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        @endif
                    </button>
                    @if(!$loop->last)
                        <svg class="w-3 h-3 text-gray-300" fill="currentColor" viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"></path></svg>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column (2/3) --}}
        <div class="lg:col-span-2 flex flex-col gap-6">

            {{-- Top Row: Balance & Amount --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @php
                    $totalStaked = $user->stakingDeposits()->where('status', 'active')->sum('amount');
                @endphp

                {{-- Balance Card --}}
                @php
                    $progressPercent = min(max($progress * 100, 0), 100);
                @endphp
                <div class="card p-6 pb-4 bg-cabinet-light-blue border-cabinet-blue/20">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="font-bold text-2xl text-cabinet-text-main">Balance</h3>
                        <div class="bg-cabinet-blue p-2 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                    </div>
                    <p class="font-manrope font-bold text-4xl text-cabinet-text-main mb-2" x-text="$store.userBalance.availableBalance.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + ' USDT'">
                        {{ number_format($user->available_balance, 0) }} USDT
                    </p>
                    <div class="w-full rounded-full h-2 mb-2 overflow-hidden" style="background-color: rgba(59, 78, 252, 0.2);" x-data="{ width: 0 }" x-init="setTimeout(() => width = {{ $progressPercent }}, 100)">
                        <div class="h-full bg-cabinet-blue rounded-full transition-all duration-1000 ease-out" style="width: {{ $progressPercent }}%" x-bind:style="'width: ' + width + '%'"></div>
                    </div>
                    <p class="text-sm text-cabinet-text-secondary">
                        @if($nextTier)
                            {{ number_format($remainingToNextTier, 2) }} USDT to reach {{ $nextTier->name }}
                        @else
                            Maximum Tier Reached
                        @endif
                    </p>
                </div>

                {{-- Amount Staked Card --}}
                <div class="card p-6 pb-4 bg-cabinet-light-yellow border-cabinet-lime/20">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="font-bold text-2xl text-cabinet-text-main">Amount Staked</h3>
                        <div class="bg-cabinet-lime p-2 rounded-lg">
                            <svg class="w-6 h-6 text-cabinet-text-main" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    <p class="font-manrope font-bold text-4xl text-cabinet-text-main mb-2">
                        {{ number_format($totalStaked, 2) }} USDT
                    </p>
                    <div class="w-full rounded-full h-2 mb-2 overflow-hidden" style="background-color: rgba(227, 255, 59, 0.3);" x-data="{ width: 0 }" x-init="setTimeout(() => width = {{ $progressPercent }}, 100)">
                        <div class="h-full bg-cabinet-lime rounded-full transition-all duration-1000 ease-out" style="width: {{ $progressPercent }}%" x-bind:style="'width: ' + width + '%'"></div>
                    </div>
                    <p class="text-sm text-cabinet-text-secondary">
                        @if($nextTier)
                            {{ number_format($remainingToNextTier, 2) }} USDT to reach {{ $nextTier->name }}
                        @else
                            Maximum Tier Reached
                        @endif
                    </p>
                </div>
            </div>

            {{-- Investment Pools --}}
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-cabinet-text-main">Investment Pools</h2>
                </div>
                <div class="js-pools-list" data-url="{{ route('cabinet.pools.data') }}" data-balance="{{ $user->available_balance }}"></div>
            </div>

            {{-- Referral Link --}}
            <div class="card p-6">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                    <div class="flex-1 w-full">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-bold text-cabinet-text-main">Referral Link</h2>
                            <button onclick="copyReferralLink()" class="text-cabinet-blue font-bold text-sm hover:underline">
                                Copy
                            </button>
                        </div>
                        <p class="text-base font-bold text-cabinet-text-main mb-3">Earn the Profits</p>
                        <div class="flex items-center gap-3 w-full">
                            <input type="text" readonly value="{{ url('/register?ref=' . $user->uuid) }}" id="referral-link" class="flex-1 bg-gray-50 border border-gray-200 text-gray-500 rounded-lg px-4 py-3 text-sm">
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
                        <h3 class="text-lg font-bold text-cabinet-text-main">Profit</h3>
                        <select id="profit-period" class="bg-gray-50 border border-gray-200 text-gray-500 text-xs rounded-md px-2 py-1 outline-none">
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                        </select>
                    </div>

                    {{-- Chart Container --}}
                    <div class="relative w-[200px] h-[200px] mx-auto">
                        <canvas id="profit-donut-chart"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <p class="text-gray-400 text-sm font-medium mb-0">Total</p>
                            <p class="text-cabinet-text-main text-2xl font-bold" id="total-profit">${{ number_format($profitChartData['completed'] + $profitChartData['expected'], 1) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Calculate Profit Section --}}
                <div class="pt-6 border-t border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-cabinet-text-main">Calculate Profit</h3>
                    </div>

                    <div class="space-y-4">
                        {{-- Pool Selection --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-600 mb-2">Select Pool</label>
                            <div class="relative">
                                <select id="calc-pool-duration" class="w-full bg-gray-50 border border-gray-200 text-cabinet-text-main rounded-lg px-4 py-3 text-sm appearance-none outline-none focus:ring-2 focus:ring-cabinet-blue/20"></select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none">
                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Amount Input --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-600 mb-2">Amount</label>
                            <div class="relative">
                                <input type="number" id="calc-amount" min="0" step="100" placeholder="Enter Amount" class="w-full bg-gray-50 border border-gray-200 text-cabinet-text-main rounded-lg px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-cabinet-blue/20">
                            </div>
                        </div>

                        {{-- Calculate Button --}}
                        <button id="calc-button" class="w-full bg-cabinet-blue text-white py-3 rounded-lg font-bold text-base hover:bg-cabinet-blue/90 transition shadow-lg mt-4 uppercase">
                            Calculate
                        </button>

                        {{-- Result --}}
                        <div class="pt-4 text-left">
                            <p class="text-base font-bold text-cabinet-text-main inline-block mr-2">Expected Profit :</p>
                            <p id="calc-result" class="text-base font-bold text-cabinet-lime block mt-1 bg-cabinet-text-main/5 p-2 rounded-md border border-cabinet-lime/20"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function copyReferralLink() {
            const input = document.getElementById('referral-link');
            input.select();
            document.execCommand('copy');
            window.dispatchEvent(new CustomEvent('show-toast', { detail: { message: 'Referral link copied!', type: 'success' } }));
        }

        // Profit Chart Logic
        let profitChart;
        const ctx = document.getElementById('profit-donut-chart').getContext('2d');

        function initChart(completed, expected) {
            if (profitChart) profitChart.destroy();

            profitChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [completed, expected],
                        backgroundColor: ['#3B4EFC', '#D9EFFF'],
                        borderWidth: 0,
                        cutout: '85%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: false }
                    }
                }
            });
        }

        initChart({{ $profitChartData['completed'] }}, {{ $profitChartData['expected'] }});

        // Calculator Logic
        const calcPoolDuration = document.getElementById('calc-pool-duration');
        const calcAmount = document.getElementById('calc-amount');
        const calcButton = document.getElementById('calc-button');
        const calcResult = document.getElementById('calc-result');

        let pools = [];

        async function loadPoolsForCalc() {
            try {
                const response = await fetch('{{ route('cabinet.pools.data') }}');
                const data = await response.json();
                pools = data.data;

                calcPoolDuration.innerHTML = pools.map(pool =>
                    `<option value="${pool.id}">${pool.name} (${pool.days} Days)</option>`
                ).join('');
            } catch (error) {
                console.error('Failed to load pools for calculator:', error);
            }
        }

        loadPoolsForCalc();

        calcButton.addEventListener('click', () => {
            const poolId = parseInt(calcPoolDuration.value);
            const amount = parseFloat(calcAmount.value);
            const pool = pools.find(p => p.id === poolId);

            if (pool && amount > 0) {
                let minProfit, maxProfit;

                if (pool.profit.includes('-')) {
                    minProfit = parseFloat(pool.profit.split('-')[0]) / 100;
                    maxProfit = parseFloat(pool.profit.split('-')[1]) / 100;
                } else {
                    minProfit = maxProfit = parseFloat(pool.profit) / 100;
                }

                const minReturn = amount * minProfit;
                const maxReturn = amount * maxProfit;

                if (minReturn === maxReturn) {
                    calcResult.innerText = `${minReturn.toFixed(2)} USDT`;
                } else {
                    calcResult.innerText = `${minReturn.toFixed(2)} USDT - ${maxReturn.toFixed(2)} USDT`;
                }
            } else {
                calcResult.innerText = 'Please enter a valid amount';
            }
        });
    </script>
    @endpush
</x-cabinet-layout>
