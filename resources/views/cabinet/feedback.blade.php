<x-cabinet-layout>
    <div class="max-w-[1030px] mx-auto">
        {{-- Header with Logo --}}
        <div class="mb-8">
            <img src="{{ asset('favicon.svg') }}" alt="Logo" class="w-16 h-16 mb-6 mx-auto">
            <div class="flex items-center gap-4">
                <a href="{{ route('cabinet.dashboard') }}" class="text-cabinet-text-main hover:opacity-70 transition">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="font-poppins font-semibold text-[24px] text-[#222222]">Contact Us</h1>
            </div>
        </div>

        {{-- Contact Form --}}
        <form action="{{ route('cabinet.contact.store') }}" method="POST" class="space-y-6" x-data="contactForm()">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- First Name --}}
                <div class="relative">
                    <input
                        type="text"
                        name="first_name"
                        x-model="form.first_name"
                        placeholder="First Name"
                        class="w-full h-[65px] px-6 bg-[#f8f8f8] border border-[#444444]/60 rounded-[6px] text-gray-900 placeholder-[#CCCCCC] text-[18px] focus:ring-1 focus:ring-cabinet-blue focus:border-cabinet-blue outline-none transition font-poppins"
                        required
                    >
                </div>
                {{-- Last Name --}}
                <div class="relative">
                    <input
                        type="text"
                        name="last_name"
                        x-model="form.last_name"
                        placeholder="Last Name"
                        class="w-full h-[65px] px-6 bg-[#f8f8f8] border border-[#444444]/60 rounded-[6px] text-gray-900 placeholder-[#CCCCCC] text-[18px] focus:ring-1 focus:ring-cabinet-blue focus:border-cabinet-blue outline-none transition font-poppins"
                        required
                    >
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Email --}}
                <div class="relative">
                    <input
                        type="email"
                        name="email"
                        x-model="form.email"
                        placeholder="Email"
                        class="w-full h-[65px] px-6 bg-[#f8f8f8] border border-[#444444]/60 rounded-[6px] text-gray-900 placeholder-[#CCCCCC] text-[18px] focus:ring-1 focus:ring-cabinet-blue focus:border-cabinet-blue outline-none transition font-poppins"
                        required
                    >
                    {{-- Figma shows a chevron for Email? Maybe it's a dropdown? If it's just a design element, we can add it or skip. The user said 'точныый ридизайн'. --}}
                    <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none">
                        <svg width="13" height="22" viewBox="0 0 13 22" fill="none" class="rotate-90">
                            <path d="M1 1L11 11L1 21" stroke="#3B4EFC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>

                {{-- Phone number --}}
                <div x-data="phoneInput()">
                    <div class="relative flex items-center h-[65px] bg-[#f8f8f8] border border-[#444444]/60 rounded-[6px] px-6 focus-within:ring-1 focus-within:ring-cabinet-blue focus-within:border-cabinet-blue transition">
                        <button
                            type="button"
                            @click="open = !open"
                            class="flex items-center gap-1 text-cabinet-blue text-[18px] font-poppins font-regular focus:outline-none"
                        >
                            <span x-text="selectedCountry.phone_code"></span>
                        </button>
                        <span class="mx-2 text-black text-[18px]">|</span>
                        <input
                            type="tel"
                            x-model="phone"
                            @input="formatPhone"
                            name="phone"
                            maxlength="19"
                            placeholder="Phone number"
                            class="flex-1 bg-transparent border-0 outline-none text-gray-900 placeholder-[#CCCCCC] text-[18px] font-poppins p-0 focus:ring-0"
                        />
                    </div>

                    {{-- Country dropdown --}}
                    <div class="relative">
                        <div
                            x-show="open"
                            @click.away="open = false"
                            x-cloak
                            class="absolute z-50 mt-1 w-full bg-white border border-[#E0E0E0] rounded-md shadow-lg max-h-60 overflow-y-auto"
                        >
                            <template x-for="country in countries" :key="country.code">
                                <button
                                    type="button"
                                    @click="selectCountry(country)"
                                    class="w-full px-4 py-2.5 text-left hover:bg-gray-100 flex items-center gap-3 font-poppins transition"
                                >
                                    <span :class="country.flag_class" class="text-base"></span>
                                    <span class="font-medium text-sm" x-text="country.phone_code"></span>
                                    <span class="text-gray-600 text-sm" x-text="country.name"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <input type="hidden" name="country_code" x-model="selectedCountry.phone_code">
                    <input type="hidden" name="country" x-model="selectedCountry.code">
                </div>
            </div>

            {{-- Message --}}
            <div class="relative">
                <textarea
                    name="message"
                    x-model="form.message"
                    placeholder="Message"
                    rows="8"
                    class="w-full px-6 py-4 bg-[#f8f8f8] border border-[#444444]/60 rounded-[6px] text-gray-900 placeholder-[#CCCCCC] text-[18px] focus:ring-1 focus:ring-cabinet-blue focus:border-cabinet-blue outline-none transition resize-none font-poppins"
                    required
                ></textarea>
            </div>

            {{-- Footer of the form --}}
            <div class="flex flex-col md:flex-row items-center justify-between gap-8 pt-4">
                {{-- Submit Button --}}
                <button
                    type="submit"
                    class="w-full md:w-[453px] h-[49px] bg-cabinet-lime text-[#262262] rounded-[6px] text-[22px] font-poppins font-semibold hover:opacity-90 transition shadow-sm flex items-center justify-center"
                >
                    Submit
                </button>

                {{-- Email and Socials --}}
                <div class="flex flex-col items-end gap-4">
                    <div class="flex items-center gap-3 text-[#444444] text-[22px] font-poppins">
                        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M30 6H6C4.35 6 3 7.35 3 9V27C3 28.65 4.35 30 6 30H30C31.65 30 33 28.65 33 27V9C33 7.35 31.65 6 30 6ZM30 9V10.23L18 18.225L6 10.23V9H30ZM6 27V13.77L18 21.765L30 13.77V27H6Z" fill="#444444"/>
                        </svg>
                        <a href="mailto:lumastake@gmail.com">lumastake@gmail.com</a>
                    </div>

                    {{-- Dynamic Social Links (Cabinet type) --}}
                    <div class="flex items-center gap-3">
                        @foreach($cabinetLinks as $link)
                            @php
                                $icon = '';
                                if ($link->platform === 'Instagram') $icon = 'fab fa-instagram';
                                elseif ($link->platform === 'Facebook') $icon = 'fab fa-facebook-f';
                                elseif ($link->platform === 'Twitter') $icon = 'fab fa-twitter';
                                elseif ($link->platform === 'Telegram') $icon = 'fab fa-telegram-plane';
                                elseif ($link->platform === 'YouTube') $icon = 'fab fa-youtube';
                                elseif ($link->platform === 'TikTok') $icon = 'fab fa-tiktok';
                            @endphp
                            @if($icon)
                                <a href="{{ $link->url }}" target="_blank" class="w-[37px] h-[37px] bg-[#3B4EFC] rounded-full flex items-center justify-center text-white hover:opacity-80 transition">
                                    <i class="{{ $icon }} text-[20px]"></i>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Success Modal --}}
    <div
        x-data="{ show: {{ session('show_confirmation_modal') ? 'true' : 'false' }} }"
        x-show="show"
        x-init="if (show) { setTimeout(() => { show = false }, 10000); }"
        @keydown.escape.window="show = false"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
        x-cloak
    >
        <div
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900/75"
            @click="show = false"
        ></div>

        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative inline-block bg-white rounded-2xl px-6 pt-6 pb-6 text-left overflow-hidden shadow-xl sm:my-8 sm:max-w-md sm:w-full"
                @click.stop
            >
                <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-green-100 mb-4">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-center text-gray-900 mb-2">Message Sent!</h3>
                <p class="text-center text-gray-600 text-sm mb-4">
                    Thank you for reaching out. We'll get back to you within 24-48 hours.
                </p>

                <button
                    @click="show = false"
                    type="button"
                    class="w-full px-4 py-2.5 bg-cabinet-blue text-white text-sm font-semibold rounded-lg hover:opacity-90 transition"
                >
                    Close
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function contactForm() {
            return {
                loading: false,
                form: {
                    first_name: '',
                    last_name: '',
                    email: '{{ auth()->user()->email }}',
                    phone: '',
                    message: ''
                }
            }
        }

        function phoneInput() {
            const countries = window.__GEOIP_COUNTRIES__ || [];
            // Default to UK as in Figma (+44)
            const defaultCountry = countries.find(c => c.phone_code === '+44') || countries[0] || {phone_code: '+44', code: 'GB', flag_class: 'fi fi-gb'};

            return {
                open: false,
                phone: '',
                countries: countries,
                selectedCountry: defaultCountry,
                selectCountry(country) {
                    this.selectedCountry = country;
                    this.open = false;
                },
                formatPhone() {
                    // Basic formatting if needed
                }
            }
        }

        @if(session('success'))
            window.addEventListener('DOMContentLoaded', () => {
                showToast('{{ session('success') }}', 'success');
            });
        @endif

        @if(session('error'))
            window.addEventListener('DOMContentLoaded', () => {
                showToast('{{ session('error') }}', 'error');
            });
        @endif
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css"/>
    @endpush
</x-cabinet-layout>
