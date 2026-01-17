<x-cabinet-layout>
    <!-- Desktop Version -->
    <div class="hidden lg:block">
        <div class="card p-6">
            <h2 class="text-xl font-bold text-cabinet-text-main mb-6">Stake History</h2>
            <div class="js-stake-history-table" data-url="{{ route('cabinet.history.data') }}"></div>
        </div>
    </div>

    <!-- Mobile Version -->
    <div class="lg:hidden bg-white rounded-xl border border-cabinet-border overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-xl font-bold text-cabinet-text-main">Stake History</h2>
        </div>

        <!-- History List -->
        <div class="p-6 space-y-4">
            @forelse($deposits as $deposit)
                @php
                    $statusBadge = match($deposit->status) {
                        'unstaked' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-500', 'label' => 'Unstaked'],
                        'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-600', 'label' => 'Completed'],
                        'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-600', 'label' => 'Cancelled'],
                        default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'label' => ucfirst($deposit->status)],
                    };
                @endphp

                <div class="border border-gray-100 rounded-xl p-6 bg-gray-50/50">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-cabinet-text-main">{{ $deposit->investmentPool->name ?? 'Pool' }}</h3>
                        <span class="px-3 py-1 {{ $statusBadge['bg'] }} {{ $statusBadge['text'] }} text-xs font-bold rounded-full uppercase">
                            {{ $statusBadge['label'] }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase">Duration</span>
                            <div class="text-sm font-bold text-cabinet-text-main">{{ $deposit->days }} Days</div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-gray-400 uppercase">Profit</span>
                            <div class="text-sm font-bold text-[#2BA6FF]">{{ number_format($deposit->percentage, 2) }}%</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4 pt-4 border-t border-gray-100">
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase">Start Date</span>
                            <div class="text-sm font-medium text-cabinet-text-main">{{ $deposit->start_date->format('d, M, Y') }}</div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-gray-400 uppercase">End Date</span>
                            <div class="text-sm font-medium text-cabinet-text-main">{{ $deposit->end_date->format('d, M, Y') }}</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <span class="text-xs font-bold text-gray-400 uppercase">Amount Staked</span>
                        <span class="text-lg font-bold text-cabinet-blue">{{ number_format($deposit->amount, 0) }} USDT</span>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center">
                    <p class="text-gray-500">No staking history available</p>
                </div>
            @endforelse
        </div>

        @if($deposits->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $deposits->links() }}
            </div>
        @endif
    </div>
</x-cabinet-layout>
