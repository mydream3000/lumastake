@if(session('show_google_setup_alert') && auth()->check() && (empty(auth()->user()->phone) || empty(auth()->user()->country)))
    <div
        x-data="{ open: true }"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-[100] overflow-y-auto"
    >
        <div class="fixed inset-0 bg-black/70 transition-opacity" @click="open = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div @click.stop
                 class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 border border-cabinet-border text-center"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0">

                <div class="w-20 h-20 bg-cabinet-blue/10 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-cabinet-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>

                <h3 class="text-2xl font-bold text-cabinet-blue mb-4">Complete Your Profile</h3>

                <p class="text-gray-600 mb-8 leading-relaxed">
                    To ensure the best experience and account security, please provide your <span class="font-semibold text-gray-900">phone number</span> and <span class="font-semibold text-gray-900">country of residence</span>.
                </p>

                <div class="flex flex-col gap-3">
                    <a href="{{ route('cabinet.profile.show') }}"
                       class="w-full py-4 bg-cabinet-blue text-white rounded-xl font-bold text-lg hover:bg-cabinet-blue/90 transition-all shadow-lg shadow-cabinet-blue/20">
                        Go to Profile
                    </a>
                    <button @click="open = false"
                            class="w-full py-3 text-gray-400 font-medium hover:text-gray-600 transition-colors">
                        Maybe later
                    </button>
                </div>
            </div>
        </div>
    </div>
    @php session()->forget('show_google_setup_alert'); @endphp
@endif
