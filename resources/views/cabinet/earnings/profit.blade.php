<x-cabinet-layout>
    <!-- Desktop Version (Таблица как раньше) -->
    <div class="hidden lg:block">
        <x-cabinet.card>
            <x-slot name="header">
                Earnings | <span class="text-cabinet-orange">Profit Earnings</span>
            </x-slot>
            <div class="card-scroll">
                <div class="js-profit-earnings-table" data-url="{{ route('cabinet.earnings.profit.data') }}"></div>
            </div>
        </x-cabinet.card>
    </div>

    <!-- Mobile Version (Новый дизайн) -->
    <div class="lg:hidden bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900">
                    Earning | <span class="text-cabinet-orange">Profit</span>
                </h2>
                <a href="{{ route('cabinet.earnings.rewards') }}" class="text-sm font-semibold text-cabinet-green hover:text-cabinet-green/80 transition-colors">
                    Rewards
                </a>
            </div>
        </div>

        <div class="p-6" x-data="profitData()" x-init="loadProfits()">
            <div x-show="loading" class="py-12 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-cabinet-green"></div>
            </div>

            <div x-show="!loading && profits.length === 0" class="py-12 text-center">
                <p class="text-gray-500">No profit earnings found</p>
            </div>

            <div x-show="!loading && profits.length > 0" class="space-y-4">
                <template x-for="profit in profits" :key="profit.id">
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-bold text-cabinet-green mb-4">Profit</h3>

                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div>
                                <span class="text-sm text-gray-600">Duration:</span>
                                <span class="text-sm font-semibold text-gray-900" x-text="profit.duration"></span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Tier:</span>
                                <span class="text-sm font-semibold text-gray-900" x-text="profit.tier"></span>
                            </div>
                            <div class="text-right">
                                <span class="text-sm text-gray-600">Profit:</span>
                                <span class="text-sm font-semibold text-cabinet-green" x-text="profit.profit + '%'"></span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Created At:</span>
                                <span class="text-sm font-semibold text-gray-900 ml-20" x-text="profit.created_at"></span>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Earned</span>
                                <span class="text-lg font-bold text-gray-900" x-text="profit.earned"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Staked Amount:</span>
                                <span class="text-base font-semibold text-gray-900" x-text="profit.invested_amount"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function profitData() {
            return {
                profits: [],
                loading: true,

                async loadProfits() {
                    try {
                        const response = await fetch('{{ route('cabinet.earnings.profit.data') }}', {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await response.json();

                        if (data.data) {
                            this.profits = data.data;
                        }
                    } catch (error) {
                        console.error('Error loading profits:', error);
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }
    </script>
    @endpush
</x-cabinet-layout>
