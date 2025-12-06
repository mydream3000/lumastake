<x-cabinet-layout>
    <div class="flex flex-col min-h-screen px-4 py-6">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column (2/3) --}}
            <div class="lg:col-span-2 flex flex-col gap-6">

                {{-- Top Row: Balance & Amount --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @php
                        // Используем правильный прогресс из контроллера
                        $progressPercent = $progress * 100;
                        $progressPercent = max(0, min(100, $progressPercent));

                        $totalStaked = $user->stakingDeposits()->where('status', 'active')->sum('amount');
                    @endphp

                    {{-- Balance Card --}}
                    <div class="relative w-full min-h-[260px] border border-[#ff00d8] bg-[#ff00d8]/10
            rounded-b-[10px] p-6 flex flex-col justify-between">

                        <div class="staked_card_decor absolute left-0 top-1/2 -translate-y-1/2
                h-[60%] w-[10px] bg-[#ff00d8]"></div>

                        <div class="relative z-10 pl-4">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="font-semibold text-[24px] text-[#222222]">Balance</h3>

                                <div class="icon_bal_amount w-[39px] h-[54px] relative flex items-center justify-center">
                                    <div class="absolute inset-0 bg-[#ff00d8] rounded-sm"></div>
                                    <img src="{{ asset('img/dashboard-img/user-icon-balance.svg') }}"  class="relative z-10 w-[39px] h-[54px] object-contain rounded-b-[10px] rounded-t-none" alt="Balance">
                                </div>
                            </div>
                            <div>
                                <p class="font-manrope font-extrabold text-[40px] text-[#484646] mb-4" x-text="$store.userBalance.displayAvailableBalance.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' USD'">
                                    {{ number_format($user->available_balance, 2) }} USD
                                </p>
                                <div class="mt-2">

                                     <div class="w-full h-[10px] bg-[#ff00d8]/20 rounded-[10px] overflow-hidden">
                                        <div class="tier-progress-bar h-full bg-[#ff00d8] rounded-[10px] transition-all duration-1000 ease-out" style="width: 0%" data-progress="{{ $progressPercent }}"></div>
                                     </div>
                                    <p class="font-medium text-[16px] text-[#101221]/70 mb-2">
                                        {{ number_format($remainingToNextTier, 2) }} USD to reach {{ $nextTier->name ?? 'Max' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="absolute right-0 bottom-0 w-[200px] h-[200px] pointer-events-none opacity-60">
                             <img src="{{ asset('img/dashboard-img/balance-ragul.svg') }}" class="w-full h-full object-contain" alt="">
                        </div>
                    </div>

                    {{-- Amount Staked Card --}}
                    <div class="relative w-full min-h-[260px] rounded-[10px] border border-[#00ffa3] bg-[#00e492]/10  p-6 flex flex-col justify-between">
                        <div class="staked_card_decor absolute left-0 top-1/2 -translate-y-1/2 h-[60%] w-[7px] bg-[#00ffa3] rounded-r-[4px]"></div>
                        <div class="relative z-10 pl-4">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="font-semibold text-[24px] text-[#222222]">Amount Staked</h3>
                                 <div class="icon_bal_amount w-[39px] h-[54px] relative flex items-center justify-center">
                                    <div class="absolute inset-0 bg-[#00e492] rounded-sm"></div>
                                    <img src="{{ asset('img/dashboard-img/amount-stake-icon.svg') }}" class="relative z-10 w-[39px] h-[54px] object-contain rounded-b-[10px] rounded-t-none" alt="Staked">
                                </div>
                            </div>
                            <div>
                                <p class="font-manrope font-extrabold text-[40px] text-[#484646] mb-4">
                                    {{ number_format($totalStaked, 2) }} USD
                                </p>
                                <div class="mt-2">

                                    <div class="w-full h-[10px] bg-[#00e492]/20 rounded-[10px] overflow-hidden">
                                        <div class="tier-progress-bar h-full bg-[#00e492] rounded-[10px] transition-all duration-1000 ease-out" style="width: 0%" data-progress="{{ $progressPercent }}"></div>
                                    </div>
                                    <p class="font-medium text-[16px] text-[#101221]/70 mb-2">
                                        {{ number_format($remainingToNextTier, 2) }} USD to reach {{ $nextTier->name ?? 'Max' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="absolute right-0 bottom-0 w-[200px] h-[200px] pointer-events-none opacity-60">
                             <img src="{{ asset('img/dashboard-img/amount-ragul.svg') }}" class="w-full h-full object-contain" alt="">
                        </div>
                    </div>
                </div>

                {{-- Investment Pools --}}
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-900">Investment Pools</h2>
                    </div>
                    <div class="js-pools-list" data-url="{{ route('cabinet.pools.data') }}" data-balance="{{ $user->available_balance }}"></div>
                </div>

                {{-- Referral Link --}}
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                        <div class="flex-1 w-full">
                            <h2 class="text-xl font-bold text-gray-900 mb-2">Referral Link</h2>
                            <p class="text-[17px] text-gray-700 mb-3">Earn the Profits</p>
                            <div class="flex items-center gap-3 w-full">
                                <input type="text" readonly value="{{ url('/register?ref=' . $user->referral_code) }}" id="referral-link" class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 rounded px-4 py-2.5 text-sm">
                                <button onclick="copyReferralLink()" class="bg-cabinet-orange text-white px-6 py-2.5 rounded font-semibold text-sm hover:opacity-90">
                                    Copy
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column (1/3): Profit & Calculator --}}
            <div class="lg:col-span-1">
                <div class="bg-white border border-[#cccccc] rounded-[10px] p-6 sticky top-6">

                    {{-- Profit Section --}}
                    <div class="mb-8 relative">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-[18px] font-semibold text-[#222222]">Profit</h3>
                            <select id="profit-period" class="bg-white border border-[#cccccc] text-[#222222] text-xs rounded-[4px] px-2 py-1 outline-none">
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                                <option value="year">This Year</option>
                            </select>
                        </div>

                        {{-- Chart Container --}}
                        <div class="relative w-[200px] h-[200px] mx-auto">
                            <canvas id="profit-donut-chart"></canvas>

                            {{-- Center Text --}}
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <p class="text-[#c5c5c5] text-[16px] font-medium mb-0">Total</p>
                                <p class="text-[#383838] text-[20px] font-semibold" id="total-profit">${{ number_format($profitChartData['completed'] + $profitChartData['expected'], 1) }}</p>
                            </div>

                            {{-- Green Badge --}}
                             <div class="absolute top-[40%] -right-4 bg-[#05c982] text-white text-[12px] font-medium px-3 py-1 rounded-[9px] shadow-sm" id="profit-badge">
                                ${{ number_format($profitChartData['completed'], 1) }}
                            </div>
                        </div>
                    </div>

                    {{-- Calculate Profit Section --}}
                    <div class="pt-6 border-t border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-[18px] font-semibold text-[#222222]">Calculate Profit</h3>
                            <div class="flex items-center gap-2 border border-[#ff451c] bg-[#ff451c]/10 px-3 py-1 rounded-[6px]">
                                <span id="calc-tier-name" class="text-[14px] font-bold text-[#ff451c]">
                                    {{-- Tier Name JS --}}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-4">
                            {{-- Pool Selection --}}
                            <div>
                                <label class="block text-[15px] font-semibold text-[#222222] mb-2">Select Pool</label>
                                <div class="relative">
                                    <select id="calc-pool-duration" class="w-full bg-[#fcfcfc] border border-[#eeeeee] text-[#222222] rounded-[8px] px-4 py-3 text-sm appearance-none outline-none">
                                        {{-- Populated by JS --}}
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none">
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </div>

                            {{-- Amount Input --}}
                            <div>
                                <label class="block text-[15px] font-semibold text-[#222222] mb-2">Amount</label>
                                <div class="relative">
                                    <input type="number" id="calc-amount" min="0" step="100" placeholder="Enter Amount" class="w-full bg-[#fcfcfc] border border-[#eeeeee] text-[#222222] rounded-[8px] px-4 py-3 text-sm outline-none">
                                </div>
                            </div>

                            {{-- Calculate Button --}}
                            <button id="calc-button" class="w-[200px] bg-[#ff451c] text-white py-3 rounded-[6px] font-bold text-[18px] hover:opacity-90 transition-opacity mt-4">
                                Calculate
                            </button>

                            {{-- Result --}}
                            <div class="pt-4 text-left">
                                <p class="text-[22px] font-semibold text-[#222222] inline-block mr-2">Expected Profit :</p>
                                <p id="calc-result" class="text-[22px] font-bold text-[#05c982] block mt-1"></p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // --- Data from Backend ---
        const allTiers = @json($allTiers);
        const userBalance = {{ $user->balance }};
        const isIslamic = {{ $user->account_type === 'islamic' ? 'true' : 'false' }};
        let profitChart = null;

        // --- Profit Chart Logic ---
        const profitCtx = document.getElementById('profit-donut-chart');

        // Plugin to draw background circle track
        const backgroundCircle = {
            id: 'backgroundCircle',
            beforeDatasetsDraw(chart, args, pluginOptions) {
                const { ctx } = chart;
                const meta = chart.getDatasetMeta(0);
                const chartArea = chart.chartArea;

                const x = (chartArea.left + chartArea.right) / 2;
                const y = (chartArea.top + chartArea.bottom) / 2;

                // Try to get radius from the first data element, or fallback calculation
                const firstElem = meta.data[0];
                const outerRadius = firstElem && firstElem.outerRadius ? firstElem.outerRadius : (Math.min(chartArea.width, chartArea.height) / 2);
                const innerRadius = firstElem && firstElem.innerRadius ? firstElem.innerRadius : (outerRadius * 0.80);

                ctx.save();
                ctx.beginPath();
                ctx.lineWidth = outerRadius - innerRadius;
                ctx.strokeStyle = '#F2F2F2';
                ctx.arc(x, y, (outerRadius + innerRadius) / 2, 0, 2 * Math.PI);
                ctx.stroke();
                ctx.restore();
            }
        };

        function initProfitChart(completed, expected, other) {
            if (profitChart) {
                profitChart.destroy();
            }

            profitChart = new Chart(profitCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Completed', 'Expected', 'Remaining'],
                    datasets: [{
                        data: [completed, expected, other],
                        backgroundColor: ['#ff451c', '#FFD2C8', 'transparent'],
                        borderWidth: 0,
                        borderRadius: 0,
                        cutout: '70%',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: false }
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                },
                plugins: [backgroundCircle]
            });
        }

        // Initialize Chart
        initProfitChart({{ $profitChartData['completed'] }}, {{ $profitChartData['expected'] }}, {{ $profitChartData['other'] }});

        // Profit Period Change
        document.getElementById('profit-period').addEventListener('change', async (e) => {
            const period = e.target.value;
            try {
                const response = await fetch(`{{ route('cabinet.dashboard.profit-by-period') }}?period=${period}`);
                const data = await response.json();
                if (data.success) {
                    initProfitChart(data.data.completed, data.data.expected, data.data.other);
                    document.getElementById('total-profit').textContent = `$${(data.data.total).toFixed(1)}`;
                    document.getElementById('profit-badge').textContent = `$${(data.data.completed).toFixed(1)}`;
                }
            } catch (error) {
                console.error('Failed to load profit data:', error);
            }
        });


        // --- Calculator Logic ---
        const durationSelect = document.getElementById('calc-pool-duration');
        const amountInput = document.getElementById('calc-amount');
        const tierNameEl = document.getElementById('calc-tier-name');
        const resultDiv = document.getElementById('calc-result');

        // Helper: Find applicable tier based on projected total
        function getProjectedTier(amount) {
            const projectedTotal = userBalance + amount;
            let targetTier = null;
            // allTiers is ordered by min_balance ascending
            for (let i = 0; i < allTiers.length; i++) {
                if (projectedTotal >= allTiers[i].min_balance) {
                    targetTier = allTiers[i];
                } else {
                    break;
                }
            }
            // Fallback to first tier if none found
            if (!targetTier && allTiers.length > 0) targetTier = allTiers[0];
            return targetTier;
        }

        // Helper: Render pool options with percentages for a specific tier
        function renderPoolOptions(tier) {
            if (!tier || !tier.pools) return;

            const currentSelection = parseInt(durationSelect.value);
            durationSelect.innerHTML = ''; // Clear existing

            // Sort pools by days
            const pools = tier.pools.sort((a, b) => a.days - b.days);

            pools.forEach(pool => {
                const option = document.createElement('option');
                option.value = pool.days;

                let percentText = '';
                if (isIslamic) {
                    percentText = `${pool.min_percentage}% - ${pool.max_percentage}%`;
                } else {
                    percentText = `${pool.percentage}%`;
                }

                option.textContent = `Pool ${pool.days} Days (${percentText})`;
                durationSelect.appendChild(option);
            });

            // Try to restore selection, otherwise select first
            const exists = pools.find(p => p.days === currentSelection);
            if (exists) {
                durationSelect.value = currentSelection;
            } else if (pools.length > 0) {
                durationSelect.value = pools[0].days;
            }
        }

        function updateCalculatorUI() {
            const amount = parseFloat(amountInput.value) || 0;
            const tier = getProjectedTier(amount);

            if (tier) {
                // Update Tier Name Badge
                tierNameEl.textContent = tier.name;
                // Update Pool Options (percentages change based on tier)
                renderPoolOptions(tier);
            }
        }

        function calculateProfit() {
            const amount = parseFloat(amountInput.value) || 0;
            const duration = parseInt(durationSelect.value);

            if (amount <= 0) {
                resultDiv.textContent = '0.00 USD';
                return;
            }

            const tier = getProjectedTier(amount);
            if (tier) {
                const pool = tier.pools.find(p => p.days === duration);
                if (pool) {
                    if (isIslamic) {
                        const minProfit = (amount * parseFloat(pool.min_percentage) / 100).toFixed(2);
                        const maxProfit = (amount * parseFloat(pool.max_percentage) / 100).toFixed(2);
                        resultDiv.textContent = `${minProfit} USD - ${maxProfit} USD`;
                    } else {
                        const profit = (amount * parseFloat(pool.percentage) / 100).toFixed(2);
                        resultDiv.textContent = `${profit} USD`;
                    }
                } else {
                    resultDiv.textContent = 'Pool not available';
                }
            }
        }

        // Event Listeners
        amountInput.addEventListener('input', updateCalculatorUI);
        document.getElementById('calc-button').addEventListener('click', calculateProfit);

        // Initialize on load
        updateCalculatorUI();

        function copyReferralLink() {
            const input = document.getElementById('referral-link');
            input.select();
            input.setSelectionRange(0, 99999);
            document.execCommand('copy');
            window.showToast('Referral link copied to clipboard!', 'success');
        }

        // Animate tier progress bars on page load
        document.addEventListener('DOMContentLoaded', function() {
            const progressBars = document.querySelectorAll('.tier-progress-bar');

            // Small delay to ensure DOM is fully rendered
            setTimeout(() => {
                progressBars.forEach(bar => {
                    const targetProgress = parseFloat(bar.getAttribute('data-progress')) || 0;
                    bar.style.width = targetProgress + '%';
                });
            }, 100);
        });
    </script>
    @endpush
</x-cabinet-layout>
