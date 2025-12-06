<x-cabinet-layout>
    <!-- Desktop Version (Таблица как раньше) -->
    <div class="hidden lg:block">
        <x-cabinet.card>
            <x-slot name="header">
                Earnings | <span class="text-cabinet-orange">Earnings Rewards</span>
            </x-slot>
            <div class="card-scroll">
                <div class="js-earnings-rewards-table" data-url="{{ route('cabinet.earnings.rewards.data') }}"></div>
            </div>
        </x-cabinet.card>
    </div>

    <!-- Mobile Version (Новый дизайн) -->
    <div class="lg:hidden bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900">
                    Earning | <span class="text-cabinet-orange">Rewards</span>
                </h2>
                <a href="{{ route('cabinet.earnings.profit') }}" class="text-sm font-semibold text-cabinet-green hover:text-cabinet-green/80 transition-colors">
                    Profits
                </a>
            </div>
        </div>

        <div class="p-6" x-data="rewardsData()" x-init="loadRewards()">
            <div x-show="loading" class="py-12 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-cabinet-green"></div>
            </div>

            <div x-show="!loading && rewards.length === 0" class="py-12 text-center">
                <p class="text-gray-500">No referral rewards found</p>
            </div>

            <div x-show="!loading && rewards.length > 0" class="space-y-4">
                <template x-for="reward in rewards" :key="reward.id">
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-bold text-cabinet-green mb-4">Rewards</h3>

                        <div class="mb-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Created At:</span>
                                <span class="text-sm font-semibold text-gray-900 ml-20" x-text="reward.date"></span>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Earned</span>
                                <span class="text-lg font-bold text-gray-900" x-text="reward.amount"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Invested Amount:</span>
                                <span class="text-base font-semibold text-gray-900" x-text="reward.profit_amount || 'N/A'"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function rewardsData() {
            return {
                rewards: [],
                loading: true,

                async loadRewards() {
                    try {
                        const response = await fetch('{{ route('cabinet.earnings.rewards.data') }}', {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await response.json();

                        if (data.data) {
                            this.rewards = data.data;
                        }
                    } catch (error) {
                        console.error('Error loading rewards:', error);
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }
    </script>
    @endpush
</x-cabinet-layout>
