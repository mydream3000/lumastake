<x-cabinet-layout>
    <div x-data="{ partnersOpen: false, totalReferrals: {{ $totalReferrals }} }">
        {{-- Desktop Header --}}
        <div class="hidden lg:flex items-center justify-between mb-8">
            <h2 class="text-3xl font-extrabold text-cabinet-text-main">Rewards</h2>
            <div class="text-cabinet-blue font-bold text-lg bg-cabinet-blue/5 px-4 py-2 rounded-lg">
                Current Level: <span class="text-2xl">{{ $currentLevel ?? 0 }}</span>
            </div>
        </div>

        {{-- Mobile Header --}}
        <div class="lg:hidden mb-6">
            <h2 class="text-2xl font-black text-cabinet-text-main mb-4 tracking-tight">Rewards</h2>
            <div class="flex items-center justify-between bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
                <span class="text-sm font-bold text-gray-400 uppercase tracking-wider">Current Level</span>
                <span class="text-xl font-black text-cabinet-blue">{{ $currentLevel ?? 0 }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Level Cards (Scrollable on Mobile) --}}
                <div class="flex lg:grid lg:grid-cols-5 gap-4 overflow-x-auto lg:overflow-visible pb-4 lg:pb-0 scrollbar-hide -mx-4 px-4 lg:mx-0 lg:px-0">
                    @php
                        $levelColors = [
                            1 => ['bg' => '#D9EEFF', 'border' => '#3B4EFC', 'shadow' => 'rgba(59, 78, 252, 0.4)'],
                            2 => ['bg' => '#DDFBE9', 'border' => '#05C982', 'shadow' => 'rgba(5, 201, 130, 0.4)'],
                            3 => ['bg' => '#E8FF59', 'border' => '#05C982', 'shadow' => 'rgba(5, 201, 130, 0.4)'],
                            4 => ['bg' => '#D6FAFF', 'border' => '#2BA6FF', 'shadow' => 'rgba(43, 166, 255, 0.4)'],
                            5 => ['bg' => '#FFEDCC', 'border' => '#FFA300', 'shadow' => 'rgba(255, 163, 0, 0.4)'],
                        ];
                    @endphp
                    @foreach($referralLevels as $level)
                        @php
                            $isActive = $currentLevel == $level->level;
                            $colors = $levelColors[$level->level] ?? ['bg' => '#F3F4F6', 'border' => '#E5E7EB', 'shadow' => 'none'];
                        @endphp
                        <div
                            class="min-w-[160px] lg:min-w-0 rounded-[24px] lg:rounded-[15px] p-5 border-2 transition-all duration-300 {{ $isActive ? 'active-level-card' : '' }}"
                            style="background-color: {{ $colors['bg'] }}; border-color: {{ $colors['border'] }}; --active-shadow: {{ $colors['shadow'] }};"
                        >
                            <h3 class="text-xl font-black lg:font-bold text-cabinet-text-main mb-6">Level {{ $level->level }}</h3>

                            <div class="flex justify-between items-center mb-3">
                                <span class="text-[10px] font-bold text-cabinet-text-main/60 uppercase tracking-tighter lg:tracking-normal">Referrals</span>
                                <span class="text-[11px] lg:text-xs font-bold text-cabinet-text-main">{{ $level->name }}</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-[10px] font-bold text-cabinet-text-main/60 uppercase">Rewards</span>
                                <span class="text-[11px] lg:text-xs font-bold text-cabinet-text-main">{{ rtrim(number_format($level->reward_percentage, 0), '.') }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Referral Link & System Info --}}
                <div class="card p-6 lg:p-8">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('img/Group-ref-users.svg') }}" class="w-7 h-7" alt="Icon">
                            <h3 class="text-xl font-black lg:font-bold text-cabinet-text-main tracking-tight">Referral Link</h3>
                        </div>
                        <div class="flex items-center gap-4 text-sm lg:text-base font-bold">
                            <span class="text-cabinet-text-main/60">Active: <span class="text-green-500 font-black">{{ $activeReferrals }}</span></span>
                            <span class="text-cabinet-text-main/60">Inactive: <span class="text-gray-400 font-black">{{ $totalReferrals - $activeReferrals }}</span></span>
                            <span class="text-cabinet-text-main/60">Total: <span class="text-cabinet-blue font-black">{{ $totalReferrals }}</span></span>
                        </div>
                    </div>

                    <div class="flex flex-col lg:flex-row lg:items-center gap-3 lg:gap-0 w-full mb-10 group">
                        <input type="text" readonly value="{{ $referralLink }}" id="referral-link-rewards" class="w-full lg:flex-1 bg-gray-50 border border-gray-200 text-gray-500 rounded-2xl lg:rounded-none lg:rounded-l-xl px-5 py-4 text-sm focus:ring-0 outline-none transition-colors group-hover:bg-white font-medium">
                        <button onclick="copyReferralLinkRewards()" class="w-full lg:w-auto bg-cabinet-blue text-white px-10 py-4 rounded-2xl lg:rounded-none lg:rounded-r-xl font-black text-base hover:bg-cabinet-blue/90 transition shadow-lg shadow-cabinet-blue/20 uppercase tracking-widest lg:tracking-normal">
                            Copy
                        </button>
                    </div>

                    {{-- Partners Button (Mobile Only) --}}
                    <div class="lg:hidden mb-10">
                        <button
                            @click="totalReferrals > 0 ? partnersOpen = true : null"
                            :class="totalReferrals > 0 ? 'bg-white border-cabinet-blue text-cabinet-blue shadow-lg active:scale-95' : 'bg-gray-100 border-gray-200 text-gray-400 cursor-not-allowed'"
                            class="w-full py-5 border-2 rounded-[24px] font-black uppercase tracking-widest text-sm transition-all flex items-center justify-center gap-3"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            Partners List
                        </button>
                    </div>

                    <div class="space-y-10">
                        <div class="pl-5 border-l-4 border-cabinet-lime">
                            <h4 class="text-lg font-black lg:font-bold text-cabinet-text-main mb-3 tracking-tight">Our Referrals System</h4>
                            <p class="text-sm text-cabinet-text-secondary leading-relaxed font-bold lg:font-medium">
                                Want to turn connections into cash? With our referral system, every share brings you closer to limitless rewards. The more you spread the word, the more you earn—start today!
                            </p>
                        </div>

                        <div>
                            <h4 class="text-lg font-black lg:font-bold text-cabinet-text-main mb-3 tracking-tight">How Our Referral System Works</h4>
                            <p class="text-sm text-cabinet-text-secondary leading-relaxed font-bold lg:font-medium">
                                The more you refer, the more you earn through our tiered rewards system
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Bottom Blocks --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="card p-6 flex items-center gap-5 border-2 border-transparent hover:border-cabinet-blue/20 transition-all group">
                        <div class="w-16 h-16 bg-cabinet-blue rounded-2xl lg:rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-cabinet-blue/20 group-hover:scale-105 transition-transform">
                            <img src="{{ asset('img/referal-tires.svg') }}" class="w-9 h-9" alt="Icon">
                        </div>
                        <div>
                            <h4 class="font-black lg:font-bold text-lg text-cabinet-text-main mb-1 tracking-tight">Tiered Rewards:</h4>
                            <p class="text-sm text-cabinet-text-secondary leading-tight font-bold lg:font-medium">Increase your earnings based on your referral level.</p>
                        </div>
                    </div>
                    <div class="card p-6 flex items-center gap-5 border-2 border-transparent hover:border-cabinet-lime/20 transition-all group">
                        <div class="w-16 h-16 bg-cabinet-lime rounded-2xl lg:rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-cabinet-lime/20 group-hover:scale-105 transition-transform">
                            <img src="{{ asset('img/referal-users.svg') }}" class="w-9 h-9" alt="Icon">
                        </div>
                        <div>
                            <h4 class="font-black lg:font-bold text-lg text-cabinet-text-main mb-1 tracking-tight">Referral Levels:</h4>
                            <p class="text-sm text-cabinet-text-secondary leading-tight font-bold lg:font-medium">Increase your earnings as you achieve referral milestones.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Desktop Right Column: Partners List --}}
            <div class="hidden lg:block lg:col-span-1">
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

        {{-- Mobile Bottom Sheet: Partners List --}}
        <div
            x-show="partnersOpen"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-400"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            class="lg:hidden fixed inset-x-0 bottom-0 z-[60] bg-white rounded-t-[40px] shadow-[0_-20px_60px_rgba(0,0,0,0.15)] overflow-hidden flex flex-col max-h-[85vh]"
            style="display: none;"
        >
            <div class="p-6 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white z-10">
                <h3 class="text-xl font-black text-cabinet-text-main">Partners List</h3>
                <button @click="partnersOpen = false" class="p-2 bg-gray-100 rounded-full text-gray-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-3 pb-10">
                @foreach($referrals as $referral)
                    <div class="bg-gray-50 rounded-3xl p-4 flex items-center justify-between border border-gray-100">
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <img src="{{ $referral->avatar_url }}" alt="{{ $referral->name }}" class="w-14 h-14 rounded-full object-cover border-2 border-white shadow-md">
                                @if($referral->active)
                                    <div class="absolute bottom-0.5 right-0.5 w-3.5 h-3.5 bg-green-500 border-2 border-white rounded-full"></div>
                                @endif
                            </div>
                            <div>
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-wider mb-0.5">Partner Name</div>
                                <div class="text-sm font-black text-cabinet-text-main tracking-tight">{{ $referral->name }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-wider mb-0.5">Email</div>
                            <div class="text-[11px] font-bold text-gray-500 truncate max-w-[120px]">{{ $referral->email }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Backdrop for Bottom Sheet --}}
        <div
            x-show="partnersOpen"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-400"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="partnersOpen = false"
            class="lg:hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-[55]"
            style="display: none;"
        ></div>
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
