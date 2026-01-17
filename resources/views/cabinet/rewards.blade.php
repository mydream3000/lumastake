<x-cabinet-layout>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                @foreach($referralLevels as $level)
                    @php
                        $isActive = $currentLevel == $level->level;
                    @endphp
                    <div class="card p-6 {{ $isActive ? 'border-cabinet-lime' : '' }}">
                        <h3 class="text-lg font-bold text-cabinet-text-dark mb-4">Level {{ $level->level }}</h3>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-cabinet-text-grey">Referrals</span>
                            <span class="text-sm font-semibold text-cabinet-text-dark">{{ $level->name }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-cabinet-text-grey">Rewards</span>
                            <span class="text-sm font-bold text-cabinet-lime">{{ rtrim(number_format($level->reward_percentage, 0), '.') }}%</span>
                        </div>
                        @if($isActive)
                            <div class="mt-3 pt-3 border-t border-cabinet-grey">
                                <div class="flex items-center justify-center gap-2 text-cabinet-lime font-semibold text-sm">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Active Level</span>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="card p-6">
                <h3 class="font-semibold text-lg text-cabinet-text-dark mb-3">How Our Referral System Works</h3>
                <p class="text-sm text-cabinet-text-grey leading-relaxed">
                    Want to turn connections into cash? With our referral system, every share brings you closer to limitless rewards. The more you spread the word, the more you earn—start today!
                </p>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <div class="card p-6">
                <h3 class="text-lg font-bold text-cabinet-text-dark mb-2">Referral Link</h3>
                <div class="flex items-center gap-3 w-full">
                    <input type="text" readonly value="{{ $referralLink }}" id="referral-link-rewards" class="flex-1 bg-cabinet-dark border border-cabinet-grey text-cabinet-text-dark rounded px-4 py-2.5 text-sm">
                    <button onclick="copyReferralLinkRewards()" class="bg-cabinet-blue text-white px-6 py-2.5 rounded font-semibold text-sm hover:opacity-90">
                        Copy
                    </button>
                </div>
            </div>
            <div class="card p-6">
                <h3 class="text-lg font-bold text-cabinet-text-dark mb-4">Your Partners</h3>
                <div class="space-y-2">
                    @forelse($referrals as $referral)
                        <div class="bg-cabinet-table-row rounded-lg p-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $referral->avatar_url }}" alt="{{ $referral->name }}" class="w-10 h-10 rounded-full object-cover">
                                <div>
                                    <div class="font-semibold text-sm {{ $referral->active ? 'text-cabinet-lime' : 'text-cabinet-text-dark' }}">{{ $referral->name }}</div>
                                    <div class="text-xs text-cabinet-text-grey">{{ $referral->email }}</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-cabinet-text-grey py-8">
                            No referrals yet.
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
