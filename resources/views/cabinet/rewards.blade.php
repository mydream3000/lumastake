<x-cabinet-layout>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                @foreach($referralLevels as $level)
                    @php
                        $isActive = $currentLevel == $level->level;
                    @endphp
                    <div class="card p-6 {{ $isActive ? 'border-cabinet-blue bg-cabinet-light-blue/20' : '' }}">
                        <h3 class="text-lg font-bold text-cabinet-text-main mb-4">Level {{ $level->level }}</h3>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-bold text-gray-400 uppercase">Referrals</span>
                            <span class="text-sm font-bold text-cabinet-text-main">{{ $level->name }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-gray-400 uppercase">Rewards</span>
                            <span class="text-sm font-bold text-[#2BA6FF]">{{ rtrim(number_format($level->reward_percentage, 0), '.') }}%</span>
                        </div>
                        @if($isActive)
                            <div class="mt-3 pt-3 border-t border-cabinet-blue/20">
                                <div class="flex items-center justify-center gap-2 text-cabinet-blue font-bold text-xs uppercase">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    <span>Active</span>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-bold text-cabinet-text-main mb-3">How Our Referral System Works</h3>
                <p class="text-base text-cabinet-text-secondary leading-relaxed">
                    Want to turn connections into cash? With our referral system, every share brings you closer to limitless rewards. The more you spread the word, the more you earn—start today!
                </p>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <div class="card p-6">
                <h3 class="text-lg font-bold text-cabinet-text-main mb-4">Referral Link</h3>
                <div class="flex items-center gap-3 w-full">
                    <input type="text" readonly value="{{ $referralLink }}" id="referral-link-rewards" class="flex-1 bg-gray-50 border border-gray-200 text-gray-500 rounded-lg px-4 py-3 text-sm">
                    <button onclick="copyReferralLinkRewards()" class="text-cabinet-blue font-bold text-sm hover:underline">
                        Copy
                    </button>
                </div>
            </div>
            <div class="card p-6">
                <h3 class="text-lg font-bold text-cabinet-text-main mb-6">Your Partners</h3>
                <div class="space-y-3">
                    @forelse($referrals as $referral)
                        <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                            <div class="flex items-center gap-4">
                                <img src="{{ $referral->avatar_url }}" alt="{{ $referral->name }}" class="w-12 h-12 rounded-full object-cover">
                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-sm text-cabinet-text-main truncate">{{ $referral->name }}</div>
                                    <div class="text-xs text-gray-400 truncate font-medium">{{ $referral->email }}</div>
                                </div>
                                @if($referral->active)
                                    <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-400 py-12">
                            <p class="text-sm font-medium">No referrals yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function copyReferralLinkRewards() {
            const input = document.getElementById('referral-link-rewards');
            navigator.clipboard.writeText(input.value).then(() => {
                window.dispatchEvent(new CustomEvent('show-toast', { detail: { message: 'Referral link copied!', type: 'success' } }));
            }).catch(() => {
                window.dispatchEvent(new CustomEvent('show-toast', { detail: { message: 'Failed to copy', type: 'error' } }));
            });
        }
    </script>
    @endpush
</x-cabinet-layout>
