<x-cabinet-layout>
    <!-- Desktop Version (Таблица как раньше) -->
    <div class="hidden lg:block">
        <x-cabinet.card>
            <x-slot name="header">
                Transaction | <span class="text-cabinet-orange">Withdraw</span>
            </x-slot>
            <div class="card-scroll">
                <div class="js-withdraw-table" data-url="{{ route('cabinet.transactions.withdraw.data') }}"></div>
            </div>
        </x-cabinet.card>
    </div>

    <!-- Mobile Version (Новый дизайн) -->
    <div class="lg:hidden bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900">
                    Transactions | <span class="text-cabinet-orange">Withdraw</span>
                </h2>
                <a href="{{ route('cabinet.transactions.deposits') }}" class="text-sm font-semibold text-cabinet-green hover:text-cabinet-green/80 transition-colors">
                    Deposit
                </a>
            </div>
        </div>

        <div class="p-6" x-data="withdrawalsData()" x-init="loadWithdrawals()">
            <div x-show="loading" class="py-12 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-cabinet-green"></div>
            </div>

            <div x-show="!loading && withdrawals.length === 0" class="py-12 text-center">
                <p class="text-gray-500">No withdrawals found</p>
            </div>

            <div x-show="!loading && withdrawals.length > 0" class="space-y-4">
                <template x-for="(withdrawal, index) in withdrawals" :key="withdrawal.id">
                    <div :class="{
                        'bg-cabinet-green/10 border-cabinet-green/30': withdrawal.status === 'completed',
                        'bg-amber-50/70 border-amber-200': withdrawal.status === 'pending',
                        'bg-orange-50 border-orange-200': withdrawal.status === 'processing',
                        'bg-red-50/50 border-red-300': withdrawal.status === 'cancelled' || withdrawal.status === 'rejected',
                        'bg-red-50/50 border-red-300': withdrawal.status === 'failed'
                    }" class="border rounded-lg p-4">
                        <div class="flex gap-4">
{{--                            <div :class="{--}}
{{--                                'bg-cabinet-green': withdrawal.status === 'completed',--}}
{{--                                'bg-pink-400': withdrawal.status === 'pending',--}}
{{--                                'bg-cabinet-orange': withdrawal.status === 'processing'--}}
{{--                            }" class="flex-shrink-0 w-12 h-12 rounded-lg flex items-center justify-center">--}}
{{--                                <span class="text-white text-lg font-bold" x-text="index + 1"></span>--}}
{{--                            </div>--}}

                            <div class="flex-1">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <div class="text-sm text-gray-600 mb-1">Transaction Type:</div>
                                        <div :class="{
                                            'text-cabinet-green': withdrawal.status === 'completed',
                                            'text-amber-700': withdrawal.status === 'pending',
                                            'text-cabinet-orange': withdrawal.status === 'processing',
                                            'text-red-600': withdrawal.status === 'cancelled' || withdrawal.status === 'rejected' || withdrawal.status === 'failed'
                                        }" class="text-base font-semibold">Withdrawal</div>
                                    </div>
                                    <span :class="{
                                        'bg-cabinet-green/20 text-cabinet-green': withdrawal.status === 'completed',
                                        'bg-amber-100/70 text-amber-700 border border-amber-300': withdrawal.status === 'pending',
                                        'bg-orange-100 text-cabinet-orange': withdrawal.status === 'processing',
                                        'bg-red-100/50 text-red-600 border border-red-400': withdrawal.status === 'cancelled' || withdrawal.status === 'rejected' || withdrawal.status === 'failed'
                                    }" class="px-3 py-1 text-sm font-medium rounded-full capitalize bg-green-100/50 text-green-600 border border-green-400" x-text="withdrawal.status === 'pending' ? 'Requested' : withdrawal.status"></span>
                                </div>

                                <div class="grid grid-cols-2 gap-4 mb-3">
                                    <div>
                                        <div class="text-sm text-gray-600 mb-1">Created At:</div>
                                        <div class="text-sm font-semibold text-gray-900" x-text="formatDate(withdrawal.created_at)"></div>
                                    </div>
                                </div>

                                <div class="space-y-1 mb-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Withdrawal In:</span>
                                        <span class="text-sm font-semibold text-gray-900" x-text="withdrawal.withdrawal_currency || 'USDT'"></span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Transaction Amount:</span>
                                        <span class="text-lg font-bold text-gray-900" x-text="withdrawal.amount"></span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <button @click="showDetails(withdrawal)" class="px-4 py-1.5 bg-cabinet-green hover:bg-cabinet-green/90 text-white text-sm font-semibold rounded-md transition-colors">
                                        Details
                                    </button>
                                    <button x-show="withdrawal.can_cancel" @click="cancelWithdrawal(withdrawal.id)" class="px-4 py-1.5 border-2 border-pink-400 text-pink-600 hover:bg-pink-50 text-sm font-semibold rounded-md transition-colors">
                                        Cancel
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
        function withdrawalsData() {
            return {
                withdrawals: [],
                loading: true,

                async loadWithdrawals() {
                    try {
                        const response = await fetch('{{ route('cabinet.transactions.withdraw.data') }}', {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await response.json();

                        if (data.success) {
                            this.withdrawals = data.data;
                        }
                    } catch (error) {
                        console.error('Error loading withdrawals:', error);
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

                showDetails(withdrawal) {
                    const statusColors = {
                        'completed': 'bg-cabinet-green/20 text-cabinet-green',
                        'pending': 'bg-amber-100/70 text-amber-700 border border-amber-300',
                        'processing': 'bg-orange-100 text-cabinet-orange',
                        'cancelled': 'bg-red-100/50 text-red-600 border border-red-400',
                        'rejected': 'bg-red-100/50 text-red-600 border border-red-400',
                        'failed': 'bg-red-100/50 text-red-600 border border-red-400'
                    };
                    const statusClass = statusColors[withdrawal.status] || 'bg-gray-100 text-gray-800';

                    window.dispatchEvent(new CustomEvent('open-modal', {
                        detail: {
                            title: 'Withdrawal Details',
                            message: `
                                <div class="space-y-4">
                                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                        <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                            <span class="text-sm text-gray-600">Transaction ID</span>
                                            <span class="font-semibold text-gray-900">#${withdrawal.id}</span>
                                        </div>

                                        <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                            <span class="text-sm text-gray-600">Amount</span>
                                            <span class="font-bold text-lg text-gray-900">${withdrawal.amount}</span>
                                        </div>

                                        <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                            <span class="text-sm text-gray-600">Withdrawal In</span>
                                            <span class="font-semibold text-gray-900">${withdrawal.withdrawal_currency || 'USDT'}</span>
                                        </div>

                                        <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                            <span class="text-sm text-gray-600">Status</span>
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full ${statusClass} capitalize">
                                                ${withdrawal.status}
                                            </span>
                                        </div>

                                        <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                            <span class="text-sm text-gray-600">Created At</span>
                                            <span class="font-semibold text-gray-900">${this.formatDate(withdrawal.created_at)}</span>
                                        </div>

                                        ${withdrawal.details && withdrawal.details.wallet_address ? `
                                            <div class="pt-2">
                                                <span class="text-sm text-gray-600 block mb-2">Receiver Address</span>
                                                <div class="bg-white rounded border border-gray-300 p-3">
                                                    <code class="text-xs text-gray-800 break-all font-mono">${withdrawal.details.wallet_address}</code>
                                                </div>
                                            </div>
                                        ` : ''}

                                        ${withdrawal.details && withdrawal.details.network ? `
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Network</span>
                                                <span class="font-semibold text-gray-900 uppercase">${withdrawal.details.network}</span>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                            `,
                            confirmText: 'Close'
                        }
                    }));
                },

                async cancelWithdrawal(id) {
                    if (!confirm('Are you sure you want to cancel this withdrawal?')) {
                        return;
                    }

                    try {
                        const response = await fetch(`/dashboard/transactions/${id}/cancel`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        const data = await response.json();

                        if (data.success) {
                            window.dispatchEvent(new CustomEvent('show-toast', {
                                detail: { message: data.message, type: 'success' }
                            }));
                            this.loadWithdrawals();
                        } else {
                            window.dispatchEvent(new CustomEvent('show-toast', {
                                detail: { message: data.message || 'Failed to cancel', type: 'error' }
                            }));
                        }
                    } catch (error) {
                        console.error('Error canceling withdrawal:', error);
                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: { message: 'Network error', type: 'error' }
                        }));
                    }
                }
            };
        }
    </script>
    @endpush
</x-cabinet-layout>
