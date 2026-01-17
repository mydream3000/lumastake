<x-cabinet-layout>
    <!-- Desktop Version -->
    <div class="hidden lg:block">
        <div class="card p-6">
            <h2 class="text-xl font-bold text-cabinet-text-main mb-6">
                Earnings | <span class="text-cabinet-blue">Earnings Rewards</span>
            </h2>
            <div class="js-earnings-rewards-table" data-url="{{ route('cabinet.earnings.rewards.data') }}"></div>
        </div>
    </div>

    <!-- Mobile Version -->
    <div class="lg:hidden bg-white rounded-xl border border-cabinet-border overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-cabinet-text-main">
                    Earning | <span class="text-cabinet-blue">Rewards</span>
                </h2>
                <a href="{{ route('cabinet.earnings.profit') }}" class="text-sm font-bold text-cabinet-blue hover:underline transition-colors">
                    Profits
                </a>
            </div>
        </div>

        <div class="p-6" x-data="rewardsData()" x-init="loadRewards()">
            <div x-show="loading" class="py-12 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-cabinet-blue"></div>
            </div>

            <div x-show="!loading && rewards.length === 0" class="py-12 text-center">
                <p class="text-gray-500">No referral rewards found</p>
            </div>

            <div x-show="!loading && rewards.length > 0" class="space-y-4">
                <template x-for="reward in rewards" :key="reward.id">
                    <div class="border border-gray-100 rounded-xl p-6 bg-gray-50/50">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-bold text-gray-400 uppercase">Date</span>
                            <span class="text-sm font-medium text-cabinet-text-main" x-text="reward.date"></span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <span class="text-xs font-bold text-gray-400 uppercase">Referral</span>
                                <div class="text-sm font-bold text-cabinet-text-main" x-text="reward.referral_name"></div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-bold text-gray-400 uppercase">Your %</span>
                                <div class="text-sm font-bold text-[#2BA6FF]" x-text="reward.reward_percentage + '%'"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4 pt-4 border-t border-gray-100">
                            <div class="col-span-2">
                                <span class="text-xs font-bold text-gray-400 uppercase">Their Profit</span>
                                <div class="text-sm font-bold text-cabinet-text-main" x-text="'$' + reward.profit_amount"></div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <span class="text-xs font-bold text-gray-400 uppercase">Your Reward</span>
                            <span class="text-lg font-bold text-green-600" x-text="'$' + reward.amount"></span>
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
