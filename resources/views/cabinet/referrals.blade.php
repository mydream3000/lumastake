<x-cabinet-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Rewards') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Level Squares -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 mb-8">
                @php
                    $levelColors = [
                        'cabinet-level-1',
                        'cabinet-level-2',
                        'cabinet-level-3',
                        'cabinet-level-4',
                        'cabinet-level-5',
                    ];
                @endphp
                @for ($i = 0; $i < 5; $i++)
                    <div class="relative p-4 rounded-lg text-center text-white font-bold"
                         style="background-color: rgba(var(--{{ str_replace('cabinet-', '', $levelColors[$i]) }}-rgb), 0.2); border: 1px solid var(--{{ str_replace('cabinet-', '', $levelColors[$i]) }}-rgb);">
                        <span class="text-lg">Level {{ $i + 1 }}</span>
                    </div>
                @endfor
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Section (2/3 width) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Bonus Table -->
                    <x-cabinet.card>
                        <x-slot name="header">Bonus Structure</x-slot>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Partners</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bonus</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr><td class="px-6 py-4 whitespace-nowrap">1–5</td><td class="px-6 py-4 whitespace-nowrap">10%</td></tr>
                                    <tr><td class="px-6 py-4 whitespace-nowrap">6–10</td><td class="px-6 py-4 whitespace-nowrap">12%</td></tr>
                                    <tr><td class="px-6 py-4 whitespace-nowrap">11–15</td><td class="px-6 py-4 whitespace-nowrap">15%</td></tr>
                                    <tr><td class="px-6 py-4 whitespace-nowrap">16–20</td><td class="px-6 py-4 whitespace-nowrap">17%</td></tr>
                                    <tr><td class="px-6 py-4 whitespace-nowrap">21+</td><td class="px-6 py-4 whitespace-nowrap">20%</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </x-cabinet.card>

                    <!-- Referral Link Block -->
                    <x-cabinet.card>
                        <x-slot name="header">
                            <div class="flex justify-between items-center">
                                <span>Referral Link</span>
                                <span class="text-sm text-gray-600">Total Referred Partners: {{ $referrals->count() }} Users</span>
                            </div>
                        </x-slot>
                        <div class="flex items-center gap-2">
                            <input id="referral-link" type="text" value="{{ url('/register?ref=' . auth()->user()->uuid) }}" readonly
                                   class="flex-1 rounded-md border-cabinet-orange shadow-sm focus:border-cabinet-orange focus:ring focus:ring-cabinet-orange focus:ring-opacity-50">
                            <x-cabinet.button onclick="copyReferralLink()">Copy</x-cabinet.button>
                        </div>
                    </x-cabinet.card>

                    <!-- Description Blocks -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-cabinet.card class="border-pink-500">
                            <x-slot name="header"><h3>Benefits of Referring</h3></x-slot>
                            <p>Refer your friends and earn a percentage of their investments. The more partners you refer, the higher your bonus!</p>
                        </x-cabinet.card>
                        <x-cabinet.card class="border-pink-500">
                            <x-slot name="header"><h3>How it Works</h3></x-slot>
                            <p>Share your unique referral link with others. When they register and invest, you automatically receive a commission.</p>
                        </x-cabinet.card>
                    </div>

                    <!-- Two Colored Blocks -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-cabinet-orange p-6 rounded-lg text-white flex items-center justify-center">
                            <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7l4-4m0 0l4 4m-4-4v18"></path></svg>
                            <span class="font-bold text-lg">Increase Your Earnings</span>
                        </div>
                        <div class="bg-cabinet-green p-6 rounded-lg text-white flex items-center justify-center">
                            <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H2v-2a4 4 0 014-4h12.356M17 20v-9a2 2 0 00-2-2H7a2 2 0 00-2 2v9m4-9h2m-2 0h2m-4 0h2m-2 0h2m-4 0h2"></path></svg>
                            <span class="font-bold text-lg">Expand Your Network</span>
                        </div>
                    </div>
                </div>

                <!-- Right Section (1/3 width) -->
                <div class="lg:col-span-1">
                    <x-cabinet.card class="border-gray-300">
                        <x-slot name="header">Referred Partners</x-slot>
                        <div class="card-scroll">
                            <div class="js-referrals-table" data-url="{{ route('cabinet.referrals.data') }}"></div>
                        </div>
                    </x-cabinet.card>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function copyReferralLink() {
            const input = document.getElementById('referral-link');
            navigator.clipboard.writeText(input.value).then(() => {
                showToast('Referral link copied!', 'success');
            }).catch(() => {
                showToast('Ошибка при копировании', 'error');
            });
        }
    </script>
    @endpush
</x-cabinet-layout>
