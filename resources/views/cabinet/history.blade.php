<x-cabinet-layout>
    <!-- Desktop Version (Таблица как раньше) -->
    <div class="hidden lg:block">
        <x-cabinet.card>
            <x-slot name="header">
                Stake History
            </x-slot>
            <div class="card-scroll">
                <div class="js-stake-history-table" data-url="{{ route('cabinet.history.data') }}"></div>
            </div>
        </x-cabinet.card>
    </div>

    <!-- Mobile Version (Новый дизайн) -->
    <div class="lg:hidden bg-white rounded-lg shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Stake History</h2>
        </div>

        <!-- History List -->
        <div class="p-6 space-y-4">
            @forelse($deposits as $deposit)
                @php
                    $borderColor = match($deposit->status) {
                        'unstaked' => 'border-pink-300',
                        'completed' => 'border-cabinet-green',
                        'cancelled' => 'border-gray-300',
                        default => 'border-gray-300',
                    };

                    $statusBadge = match($deposit->status) {
                        'unstaked' => ['bg' => 'bg-pink-100', 'text' => 'text-pink-600', 'label' => 'Unstaked'],
                        'completed' => ['bg' => 'bg-cabinet-green/10', 'text' => 'text-cabinet-green', 'label' => 'Completed'],
                        'cancelled' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'label' => 'Cancelled'],
                        default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'label' => 'Unknown'],
                    };
                @endphp

                <div class="border-2 {{ $borderColor }} rounded-lg p-6 bg-white">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $deposit->investmentPool->name ?? 'Diamond Elite' }}</h3>
                        <span class="px-3 py-1 {{ $statusBadge['bg'] }} {{ $statusBadge['text'] }} text-sm font-medium rounded-full">
                            {{ $statusBadge['label'] }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <span class="text-sm text-gray-600">Duration:</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $deposit->days }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <span class="text-sm text-gray-600">Start Date:</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $deposit->start_date->format('d, M, Y') }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-sm text-gray-600">End Date:</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $deposit->end_date->format('d, M, Y') }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-600 mb-1">Amount Staked</div>
                            <div class="text-sm text-gray-600">Profit</div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-semibold text-gray-900 mb-1">{{ number_format($deposit->amount, 0) }}</div>
                            <div class="text-base font-semibold text-cabinet-green">{{ number_format($deposit->percentage, 2) }}%</div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center">
                    <p class="text-gray-500">No staking history available</p>
                </div>
            @endforelse
        </div>

        @if($deposits->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $deposits->links() }}
            </div>
        @endif
    </div>
</x-cabinet-layout>
