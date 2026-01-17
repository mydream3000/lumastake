<x-cabinet-layout>
    <!-- Desktop Version (Таблица как раньше) -->
    <div class="hidden lg:block">
        <x-cabinet.card>
            <x-slot name="header">
                Staking
            </x-slot>

            <div class="card-scroll">
                <div class="js-staking-table" data-url="{{ route('cabinet.staking.data') }}"></div>
            </div>
        </x-cabinet.card>
    </div>

    <!-- Mobile Version (Новый дизайн) -->
    <div class="lg:hidden bg-cabinet-dark rounded-lg shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-cabinet-grey">
            <h2 class="text-xl font-bold text-cabinet-text-dark">Manage Staking</h2>
        </div>

        <!-- Stakes List -->
        <div class="p-6 space-y-4">
            @forelse($stakes as $stake)
                @php
                    $now = now();
                    $daysLeft = (int) $now->diffInDays($stake->end_date, false);
                    $daysLeft = max(0, $daysLeft);
                @endphp
                <div class="border-2 border-cabinet-orange rounded-lg p-6 bg-cabinet-dark">
                    <!-- Header Row -->
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-cabinet-text-dark">{{ $stake->investmentPool->name ?? 'Diamond Elite' }}</h3>
                        <span class="flex items-center gap-2 px-3 py-1 bg-cabinet-green/10 text-cabinet-green text-sm font-medium rounded-full">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                            {{ $daysLeft }} Days Left
                        </span>
                    </div>

                    <!-- Info Grid -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <span class="text-sm text-cabinet-text-grey">Duration:</span>
                            <span class="text-sm font-semibold text-cabinet-text-dark">{{ $stake->days }} Days</span>
                        </div>
                        <div class="text-right">
                            <span class="text-sm text-cabinet-text-grey">Start Date:</span>
                            <span class="text-sm font-semibold text-cabinet-text-dark">{{ $stake->start_date->format('d, M, Y') }}</span>
                        </div>
                    </div>

                    <!-- Amount & Profit -->
                    <div class="flex flex-col justify-between mb-4">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm text-cabinet-text-grey">Amount Staked</span>
                            <span class="text-lg font-semibold text-cabinet-text-dark">{{ number_format($stake->amount, 0) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-cabinet-text-grey">Profit</span>
                            <span class="text-base font-semibold text-cabinet-green">
                                @php
                                    $displayProfit = number_format($stake->percentage, 2) . '%';
                                    if (auth()->user()->account_type === 'islamic' && $stake->percentage == 0) {
                                        $pool = \App\Models\InvestmentPoolIslamic::where('tier_id', $stake->tier_id)
                                            ->where('duration_days', $stake->days)
                                            ->first();
                                        if ($pool) {
                                            $displayProfit = $pool->min_percentage . '% - ' . $pool->max_percentage . '%';
                                        } else {
                                            $displayProfit = 'Pending';
                                        }
                                    }
                                @endphp
                                {{ $displayProfit }}
                            </span>
                        </div>
                    </div>
                    <!-- Actions Row -->
                    <div class="flex items-center justify-between">
                        <!-- Auto Stake Toggle -->
                        <div class="flex items-center gap-3" x-data="{ tooltipOpen: false }">
                            <div class="flex items-center gap-1 relative">
                                <span class="text-sm font-medium text-cabinet-text-grey">Auto stake</span>
                                <!-- Tooltip Icon -->
                                <button @click="tooltipOpen = !tooltipOpen" type="button" class="inline-flex items-center justify-center w-4 h-4 text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                                    </svg>
                                </button>

                                <!-- Tooltip Popup -->
                                <div x-show="tooltipOpen"
                                     @click.away="tooltipOpen = false"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute z-50 w-64 p-3 text-sm text-cabinet-text-grey bg-cabinet-dark border border-cabinet-grey rounded-lg shadow-lg bottom-full left-0 mb-2"
                                     style="display: none;">
                                    <p>Auto Stake automatically restakes your initial investment when the period ends.</p>
                                </div>
                            </div>

                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                       class="sr-only peer"
                                       {{ $stake->auto_renewal ? 'checked' : '' }}
                                       onchange="toggleAutoRenewal({{ $stake->id }}, this.checked)">
                                <div class="w-11 h-6 bg-cabinet-grey peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-cabinet-orange/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cabinet-orange"></div>
                            </label>
                        </div>

                        <!-- Un-Stake Button -->
                        <button onclick="unstake({{ $stake->id }})" class="px-6 py-2 bg-cabinet-orange hover:bg-cabinet-orange/90 text-white text-sm font-semibold rounded-md transition-colors">
                            Un-Stake
                        </button>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center">
                    <p class="text-cabinet-text-grey">No active staking deposits</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($stakes->hasPages())
            <div class="px-6 py-4 border-t border-cabinet-grey">
                {{ $stakes->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        function toggleAutoRenewal(stakeId, enabled) {
            fetch(`/dashboard/staking/${stakeId}/auto-renewal`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ auto_renewal: enabled })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { message: data.message, type: 'success' }
                    }));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: { message: 'Failed to update auto-renewal', type: 'error' }
                }));
            });
        }

        function unstake(stakeId) {
            if (!confirm('Are you sure you want to un-stake? You will lose 10% as a penalty.')) {
                return;
            }

            fetch(`/dashboard/staking/${stakeId}/unstake`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { message: data.message, type: 'success' }
                    }));
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { message: data.message || 'Failed to unstake', type: 'error' }
                    }));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: { message: 'Network error', type: 'error' }
                }));
            });
        }
    </script>
    @endpush
</x-cabinet-layout>
