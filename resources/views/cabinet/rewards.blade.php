<x-cabinet-layout>
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-3xl font-extrabold text-cabinet-text-main">Rewards</h2>
        <div class="text-cabinet-blue font-bold text-lg bg-cabinet-blue/5 px-4 py-2 rounded-lg">
            Current Level: <span class="text-2xl">{{ $currentLevel ?? 1 }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left Column --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Level Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                @php
                    $levelColors = [
                        1 => ['bg' => '#D9EFFF', 'border' => '#3B4EFC', 'shadow' => 'rgba(59, 78, 252, 0.4)'],
                        2 => ['bg' => '#E1F8F0', 'border' => '#05C982', 'shadow' => 'rgba(5, 201, 130, 0.4)'],
                        3 => ['bg' => '#FEFFD6', 'border' => '#E3FF3B', 'shadow' => 'rgba(227, 255, 59, 0.4)'],
                        4 => ['bg' => '#E1FBFF', 'border' => '#2BA6FF', 'shadow' => 'rgba(43, 166, 255, 0.4)'],
                        5 => ['bg' => '#FFF2D9', 'border' => '#FFA300', 'shadow' => 'rgba(255, 163, 0, 0.4)'],
                    ];
                @endphp
                @foreach($referralLevels as $level)
                    @php
                        $isActive = $currentLevel == $level->level;
                        $colors = $levelColors[$level->level] ?? ['bg' => '#F3F4F6', 'border' => '#E5E7EB', 'shadow' => 'none'];
                    @endphp
                    <div
                        class="rounded-[15px] p-5 border-2 transition-all duration-300 {{ $isActive ? 'active-level-card' : '' }}"
                        style="background-color: {{ $colors['bg'] }}; border-color: {{ $isActive ? $colors['border'] : 'transparent' }}; --active-shadow: {{ $colors['shadow'] }};"
                    >
                        <h3 class="text-xl font-bold text-cabinet-text-main mb-6">Level {{ $level->level }}</h3>

                        <div class="flex justify-between items-center mb-3">
                            <span class="text-[10px] font-bold text-cabinet-text-main/60 uppercase">Referrals</span>
                            <span class="text-xs font-bold text-cabinet-text-main">{{ $level->name }}</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-bold text-cabinet-text-main/60 uppercase">Rewards</span>
                            <span class="text-xs font-bold text-cabinet-text-main">{{ rtrim(number_format($level->reward_percentage, 0), '.') }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Referral Link & System Info --}}
            <div class="card p-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('img/Group-ref-users.svg') }}" class="w-7 h-7" alt="Icon">
                        <h3 class="text-xl font-bold text-cabinet-text-main">Referral Link</h3>
                    </div>
                    <div class="text-base font-medium text-cabinet-text-main/60">
                        Total Referred Partners: <span class="text-cabinet-lime font-bold text-lg">{{ $totalReferrals }} users</span>
                    </div>
                </div>

                <div class="flex items-center gap-0 w-full mb-10 group">
                    <input type="text" readonly value="{{ $referralLink }}" id="referral-link-rewards" class="flex-1 bg-gray-50 border border-gray-200 text-gray-500 rounded-l-xl px-5 py-4 text-sm focus:ring-0 outline-none transition-colors group-hover:bg-white">
                    <button onclick="copyReferralLinkRewards()" class="bg-cabinet-blue text-white px-10 py-4 rounded-r-xl font-bold text-base hover:bg-cabinet-blue/90 transition shadow-lg shadow-cabinet-blue/20 uppercase">
                        Copy
                    </button>
                </div>

                <div class="space-y-10">
                    <div class="pl-5 border-l-4 border-cabinet-lime">
                        <h4 class="text-lg font-bold text-cabinet-text-main mb-3">Our Referrals System</h4>
                        <p class="text-sm text-cabinet-text-secondary leading-relaxed font-medium">
                            Want to turn connections into cash? With our referral system, every share brings you closer to limitless rewards. The more you spread the word, the more you earn—start today!
                        </p>
                    </div>

                    <div>
                        <h4 class="text-lg font-bold text-cabinet-text-main mb-3">How Our Referral System Works</h4>
                        <p class="text-sm text-cabinet-text-secondary leading-relaxed font-medium">
                            The more you refer, the more you earn through our tiered rewards system
                        </p>
                    </div>
                </div>
            </div>

            {{-- Bottom Blocks --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="card p-6 flex items-center gap-5 border-2 border-transparent hover:border-cabinet-blue/20 transition-all group">
                    <div class="w-16 h-16 bg-cabinet-blue rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-cabinet-blue/20 group-hover:scale-105 transition-transform">
                        <img src="{{ asset('img/referal-tires.svg') }}" class="w-9 h-9" alt="Icon">
                    </div>
                    <div>
                        <h4 class="font-bold text-lg text-cabinet-text-main mb-1">Tiered Rewards:</h4>
                        <p class="text-sm text-cabinet-text-secondary leading-tight">Increase your earnings based on your referral level.</p>
                    </div>
                </div>
                <div class="card p-6 flex items-center gap-5 border-2 border-transparent hover:border-cabinet-lime/20 transition-all group">
                    <div class="w-16 h-16 bg-cabinet-lime rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-cabinet-lime/20 group-hover:scale-105 transition-transform">
                        <img src="{{ asset('img/referal-users.svg') }}" class="w-9 h-9" alt="Icon">
                    </div>
                    <div>
                        <h4 class="font-bold text-lg text-cabinet-text-main mb-1">Referral Levels:</h4>
                        <p class="text-sm text-cabinet-text-secondary leading-tight">Increase your earnings as you achieve referral milestones.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Partners List --}}
        <div class="lg:col-span-1">
            <div class="card p-0 overflow-hidden flex flex-col h-full border-2 border-transparent shadow-xl">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-cabinet-text-main uppercase tracking-wider">Name</span>
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 5.83L15.17 9l1.41-1.41L12 3 7.41 7.59 8.83 9 12 5.83zm0 12.34L8.83 15l-1.41 1.41L12 21l4.59-4.59L15.17 15 12 18.17z"></path></svg>
                    </div>
                    <span class="text-sm font-bold text-cabinet-text-main uppercase tracking-wider">Email</span>
                </div>

                <div class="flex-1 overflow-y-auto max-h-[750px] scrollbar-thin scrollbar-thumb-gray-200">
                    @forelse($referrals as $referral)
                        <div class="p-4 border-b border-gray-50 flex items-center justify-between hover:bg-cabinet-blue/[0.02] transition-colors group">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <img src="{{ $referral->avatar_url }}" alt="{{ $referral->name }}" class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm">
                                    @if($referral->active)
                                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                                    @endif
                                </div>
                                <span class="text-sm font-bold text-cabinet-text-main group-hover:text-cabinet-blue transition-colors">{{ $referral->name }}</span>
                            </div>
                            <span class="text-sm text-gray-500 font-medium">{{ $referral->email }}</span>
                        </div>
                    @empty
                        <div class="p-20 text-center text-gray-400">
                            <div class="mb-4 flex justify-center">
                                <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            <p class="text-sm font-medium">No partners yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes pulse-shadow {
            0% { box-shadow: 0 0 0 0 var(--active-shadow); }
            70% { box-shadow: 0 0 0 10px rgba(255, 255, 255, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
        }
        .active-level-card {
            animation: pulse-shadow 2s infinite;
            z-index: 10;
        }

        /* Scrollbar styles */
        .scrollbar-thin::-webkit-scrollbar {
            width: 4px;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: transparent;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #E5E7EB;
            border-radius: 10px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #D1D5DB;
        }
    </style>

    @push('scripts')
    <script>
        function copyReferralLinkRewards() {
            const input = document.getElementById('referral-link-rewards');
            input.select();
            document.execCommand('copy');
            window.dispatchEvent(new CustomEvent('show-toast', { detail: { message: 'Referral link copied!', type: 'success' } }));
        }
    </script>
    @endpush
</x-cabinet-layout>
