<x-cabinet-layout>
    <!-- Desktop Version (Таблица как раньше) -->
    <div class="hidden lg:block">
        <x-cabinet.card>
            <x-slot name="header">
                Transaction | <span class="text-cabinet-orange">Deposits</span>
            </x-slot>
            <div class="card-scroll">
                <div class="js-deposits-table" data-url="{{ route('cabinet.transactions.deposits.data') }}"></div>
            </div>
        </x-cabinet.card>
    </div>

    <!-- Mobile Version (Новый дизайн) -->
    <div class="lg:hidden bg-cabinet-dark rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-cabinet-grey">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-cabinet-text-dark">
                    Transactions | <span class="text-cabinet-orange">Deposits</span>
                </h2>
                <a href="{{ route('cabinet.transactions.withdraw') }}" class="text-sm font-semibold text-cabinet-green hover:text-cabinet-green/80 transition-colors">
                    Withdraw
                </a>
            </div>
        </div>

        <div class="p-6" x-data="depositsData()" x-init="loadDeposits()">
            <div x-show="loading" class="py-12 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-cabinet-green"></div>
            </div>

            <div x-show="!loading && deposits.length === 0" class="py-12 text-center">
                <p class="text-cabinet-text-grey">No deposits found</p>
            </div>

            <div x-show="!loading && deposits.length > 0" class="space-y-4">
                <template x-for="(deposit, index) in deposits" :key="deposit.id">
                    <div
                        :class="{
                            'bg-cabinet-green/10 border-cabinet-green/30': deposit.status === 'confirmed',
                            'bg-amber-500/10 border-amber-500/30': deposit.status === 'pending',
                            'bg-red-500/10 border-red-500/30': deposit.status === 'failed',
                            'bg-gray-700/20 border-gray-700/30': deposit.status === 'cancelled'
                        }"
                        class="border rounded-lg p-4"
                    >
                        <div class="flex gap-4">
                            <div
                                :class="{
                                    'bg-cabinet-green': deposit.status === 'confirmed',
                                    'bg-amber-500': deposit.status === 'pending',
                                    'bg-red-500': deposit.status === 'failed',
                                    'bg-gray-500': deposit.status === 'cancelled'
                                }"
                                class="flex-shrink-0 w-12 h-12 rounded-lg flex items-center justify-center"
                            >
                                <span class=" text-white text-lg font-bold" x-text="index + 1"></span>
                            </div>

                            <div class="flex-1">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <div class="text-sm text-cabinet-text-grey mb-1">Transaction Type:</div>
                                        <div
                                            :class="{
                                                'text-cabinet-green': deposit.status === 'confirmed',
                                                'text-amber-500': deposit.status === 'pending',
                                                'text-red-500': deposit.status === 'failed',
                                                'text-gray-400': deposit.status === 'cancelled'
                                            }"
                                            class="text-base font-semibold"
                                        >
                                            Deposits
                                        </div>
                                    </div>
                                    <span
                                        :class="{
                                            'bg-cabinet-green/20 text-cabinet-green': deposit.status === 'confirmed',
                                            'bg-amber-500/20 text-amber-500': deposit.status === 'pending',
                                            'bg-red-500/20 text-red-500': deposit.status === 'failed',
                                            'bg-gray-700/20 text-gray-400': deposit.status === 'cancelled'
                                        }"
                                        class="px-3 py-1 text-sm font-medium rounded-full capitalize"
                                        x-text="deposit.status"
                                    >
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-4 mb-3">
                                    <div>
                                        <div class="text-sm text-cabinet-text-grey mb-1">Created At:</div>
                                        <div class="text-sm font-semibold text-cabinet-text-dark" x-text="formatDate(deposit.created_at)"></div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-cabinet-text-grey mb-1">Transaction Amount:</div>
                                        <div class="text-lg font-bold text-cabinet-text-dark" x-text="deposit.amount"></div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <button
                                        @click="showDetails(deposit)"
                                        :class="{
                                            'bg-cabinet-green hover:bg-cabinet-green/90': deposit.status === 'confirmed',
                                            'bg-amber-500 hover:bg-amber-600': deposit.status === 'pending',
                                            'bg-red-500 hover:bg-red-600': deposit.status === 'failed',
                                            'bg-gray-500 hover:bg-gray-600': deposit.status === 'cancelled'
                                        }"
                                        class="px-4 py-1.5 border-amber-600 text-white text-sm font-semibold rounded-md transition-colors"
                                    >
                                        Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function depositsData() {
            return {
                deposits: [],
                loading: true,

                async loadDeposits() {
                    try {
                        const response = await fetch('{{ route('cabinet.transactions.deposits.data') }}', {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await response.json();

                        if (data.success) {
                            this.deposits = data.data;
                        }
                    } catch (error) {
                        console.error('Error loading deposits:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                formatDate(isoDate) {
                    const date = new Date(isoDate);
                    return date.toLocaleDateString('en-US', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric'
                    });
                },

                showDetails(deposit) {
                    const statusColors = {
                        'confirmed': 'bg-cabinet-green/20 text-cabinet-green',
                        'pending': 'bg-cabinet-orange/20 text-cabinet-orange',
                        'failed': 'bg-red-500/20 text-red-500',
                        'cancelled': 'bg-gray-700/20 text-gray-400'
                    };
                    const statusClass = statusColors[deposit.status] || 'bg-gray-700/20 text-gray-400';

                    window.dispatchEvent(new CustomEvent('open-modal', {
                        detail: {
                            title: 'Deposit Details',
                            message: `
                                <div class="space-y-4">
                                    <div class="bg-cabinet-dark rounded-lg p-4 space-y-3">
                                        <div class="flex justify-between items-center border-b border-cabinet-grey pb-2">
                                            <span class="text-sm text-cabinet-text-grey">Transaction ID</span>
                                            <span class="font-semibold text-cabinet-text-dark">#${deposit.id}</span>
                                        </div>

                                        <div class="flex justify-between items-center border-b border-cabinet-grey pb-2">
                                            <span class="text-sm text-cabinet-text-grey">Type</span>
                                            <span class="font-semibold text-cabinet-text-dark">${deposit.type}</span>
                                        </div>

                                        <div class="flex justify-between items-center border-b border-cabinet-grey pb-2">
                                            <span class="text-sm text-cabinet-text-grey">Amount</span>
                                            <span class="font-bold text-lg text-cabinet-text-dark">${deposit.amount}</span>
                                        </div>

                                        <div class="flex justify-between items-center border-b border-cabinet-grey pb-2">
                                            <span class="text-sm text-cabinet-text-grey">Status</span>
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full ${statusClass} capitalize">
                                                ${deposit.status}
                                            </span>
                                        </div>

                                        <div class="flex justify-between items-center border-b border-cabinet-grey pb-2">
                                            <span class="text-sm text-cabinet-text-grey">Created At</span>
                                            <span class="font-semibold text-cabinet-text-dark">${this.formatDate(deposit.created_at)}</span>
                                        </div>
                                    </div>
                                </div>
                            `,
                            confirmText: 'Close'
                        }
                    }));
                }
            };
        }
    </script>
    @endpush
</x-cabinet-layout>
