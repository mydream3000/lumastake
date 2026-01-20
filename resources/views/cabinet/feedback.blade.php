<x-cabinet-layout>
    <!-- Mobile Header (Fixed to Top) -->
    <div class="lg:hidden fixed top-0 left-0 right-0 bg-[#1F212E] px-4 py-3 z-40">
        <div class="flex items-center justify-between">
            <h1 class="text-white text-lg font-medium">
                Back to Lumastake <span class="text-cabinet-orange">Dashboard</span>
            </h1>
            <a href="{{ route('cabinet.dashboard') }}" class="text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
        </div>
    </div>
    <!-- Mobile Top Spacer (to prevent content from going under fixed header) -->
    <div class="lg:hidden h-14"></div>
    <!-- Desktop Header -->
    <div class="hidden lg:block mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Contact Us</h1>
            <a href="{{ route('cabinet.dashboard') }}" class="text-cabinet-orange hover:text-cabinet-orange/80 font-medium">
                ← Back to Dashboard
            </a>
        </div>
    </div>

    <div class="max-w-2xl mx-auto">
        <!-- Logo -->
        <div class="flex justify-center mb-6 lg:mb-8">
            <svg class="w-24 h-24 lg:w-32 lg:h-32" viewBox="0 0 200 200" fill="none">
                <defs>
                    <linearGradient id="logo-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" style="stop-color:#FF00D8;stop-opacity:1" />
                        <stop offset="50%" style="stop-color:#05C982;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#00E0FF;stop-opacity:1" />
                    </linearGradient>
                </defs>
                <path d="M100 20 L180 80 L150 160 L50 160 L20 80 Z" fill="url(#logo-gradient)" />
            </svg>
        </div>

        <!-- Title -->
        <h2 class="text-2xl lg:text-3xl font-bold text-center text-gray-900 mb-8 lg:mb-10">Contact Us</h2>

        <!-- Contact Form -->
        <form action="{{ route('cabinet.contact.store') }}" method="POST" class="space-y-4 lg:space-y-5" x-data="contactForm()">
            @csrf

            <!-- First Name -->
            <div>
                <input
                    type="text"
                    name="first_name"
                    x-model="form.first_name"
                    placeholder="First Name"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cabinet-orange/50 focus:border-cabinet-orange transition"
                    required
                >
            </div>

            <!-- Last Name -->
            <div>
                <input
                    type="text"
                    name="last_name"
                    x-model="form.last_name"
                    placeholder="Last Name"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cabinet-orange/50 focus:border-cabinet-orange transition"
                    required
                >
            </div>

            <!-- Email -->
            <div>
                <input
                    type="email"
                    name="email"
                    x-model="form.email"
                    placeholder="Email"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cabinet-orange/50 focus:border-cabinet-orange transition"
                    required
                >
            </div>

            <!-- Phone -->
            <div class="relative" x-data="phoneInput()">
                <div class="flex w-full items-stretch rounded-lg border border-gray-300 bg-gray-50 overflow-hidden focus-within:border-cabinet-orange focus-within:ring-2 focus-within:ring-cabinet-orange/50">
                    <button
                        type="button"
                        @click="open = !open"
                        class="text-sm px-4 py-3 bg-gray-50 text-gray-700 flex items-center gap-2 select-none hover:bg-gray-100 transition"
                    >
                        <span class="text-xl" x-text="selectedCountry.flag"></span>
                        <span x-text="selectedCountry.phone_code"></span>
                        <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <input
                        type="tel"
                        x-model="phone"
                        @input="formatPhone"
                        name="phone"
                        maxlength="19"
                        placeholder="123 456 7890"
                        class="px-4 py-3 flex-1 min-w-0 bg-transparent border-0 outline-none text-sm text-gray-900 placeholder-gray-400"
                    />
                </div>

                <!-- Выпадающий список стран -->
                <div
                    x-show="open"
                    @click.away="open = false"
                    class="absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg max-h-80 overflow-y-auto"
                    style="display:none;"
                >
                    <template x-for="country in countries" :key="country.code">
                        <button
                            type="button"
                            @click="selectCountry(country)"
                            class="w-full px-4 py-3 text-left hover:bg-gray-100 flex items-center gap-3 transition"
                        >
                            <span class="text-xl" x-text="country.flag"></span>
                            <span class="font-medium" x-text="country.phone_code"></span>
                            <span class="text-gray-600" x-text="country.name"></span>
                        </button>
                    </template>
                </div>

                <input type="hidden" name="country_code" x-model="selectedCountry.phone_code">
                <input type="hidden" name="country" x-model="selectedCountry.code">
            </div>

            <!-- Message -->
            <div>
                <textarea
                    name="message"
                    x-model="form.message"
                    placeholder="Message"
                    rows="5"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cabinet-orange/50 focus:border-cabinet-orange transition resize-none"
                    required
                ></textarea>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                class="w-full px-6 py-3.5 bg-cabinet-orange text-white rounded-lg text-lg font-semibold hover:bg-cabinet-orange/90 transition disabled:opacity-50"
            >
                Submit
            </button>
        </form>

        <!-- Success Confirmation Modal -->
        <div
            x-data="{ show: {{ session('show_confirmation_modal') ? 'true' : 'false' }} }"
            x-show="show"
            x-init="if (show) {
                setTimeout(() => { show = false }, 10000);
            }"
            @keydown.escape.window="show = false"
            class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;"
            x-cloak
        >
            <!-- Overlay -->
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

            <!-- Modal Content -->
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative inline-block bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-8"
                    @click.stop
                >
                    <!-- Success Icon -->
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                        <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <!-- Title -->
                    <h3 class="text-2xl font-bold text-center text-gray-900 mb-3">
                        Message Received!
                    </h3>

                    <!-- Message -->
                    <div class="text-center mb-6">
                        <p class="text-gray-600 mb-4">
                            Thank you for reaching out to us! We have successfully received your message and our support team will review it shortly.
                        </p>
                        <p class="text-sm text-gray-500">
                            You will receive a confirmation email with details of your submission. We'll get back to you within 24-48 hours.
                        </p>
                    </div>

                    <!-- Auto-close timer -->
                    <div class="text-center mb-4">
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 rounded-lg text-sm text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>This window will close automatically in 10 seconds</span>
                        </div>
                    </div>

                    <!-- Close Button -->
                    <div class="text-center">
                        <button
                            @click="show = false"
                            type="button"
                            class="inline-flex justify-center px-6 py-3 bg-cabinet-orange text-white text-base font-semibold rounded-lg hover:bg-cabinet-orange/90 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cabinet-orange"
                        >
                            Close
                        </button>
                    </div>

                    <!-- Close Icon -->
                    <button
                        @click="show = false"
                        type="button"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Email & Social Links -->
        <div class="mt-8 text-center">
            <a href="mailto:support@lumastake.com" class="inline-flex items-center gap-2 text-gray-600 hover:text-cabinet-orange transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                support@lumastake.com
            </a>

            <div class="flex justify-center gap-4 mt-4">
                <a href="#" class="w-10 h-10 bg-cabinet-orange rounded-full flex items-center justify-center text-white hover:bg-cabinet-orange/90 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.477 2 2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.879V14.89h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.989C18.343 21.129 22 16.99 22 12c0-5.523-4.477-10-10-10z"/>
                    </svg>
                </a>
                <a href="#" class="w-10 h-10 bg-cabinet-orange rounded-full flex items-center justify-center text-white hover:bg-cabinet-orange/90 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                    </svg>
                </a>
                <a href="#" class="w-10 h-10 bg-cabinet-orange rounded-full flex items-center justify-center text-white hover:bg-cabinet-orange/90 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                </a>
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
            return {
                open: false,
                phone: '',
                countries: [],
                selectedCountry: { code: 'US', name: 'United States', phone_code: '+1', flag: '🇺🇸' },

                async init() {
                    // Load all countries
                    try {
                        const response = await fetch('/api/geoip/countries');
                        const data = await response.json();
                        if (data.success) {
                            this.countries = data.countries;
                        }
                    } catch (error) {
                        console.error('Failed to load countries:', error);
                        // Fallback to v1
                        try {
                            const resp = await fetch('/api/v1/geoip/countries');
                            const d = await resp.json();
                            if (d.success) this.countries = d.countries;
                        } catch(e) {}
                    }

                    // Get country by IP
                    try {
                        const response = await fetch('/api/geoip/country');
                        const data = await response.json();
                        if (data.success && data.country) {
                            const country = this.countries.find(c => c.code === data.country.country_code);
                            if (country) {
                                this.selectedCountry = country;
                            }
                        }
                    } catch (error) {
                        console.error('Failed to detect country:', error);
                    }
                },

                selectCountry(country) {
                    this.selectedCountry = country;
                    this.open = false;
                },

                formatPhone(event) {
                    // Удаляем все кроме цифр
                    let value = event.target.value.replace(/\D/g, '');

                    // Ограничиваем максимум 15 цифрами
                    if (value.length > 15) {
                        value = value.slice(0, 15);
                    }

                    // Форматируем с пробелами (группы по 3 цифры)
                    let formatted = '';
                    for (let i = 0; i < value.length; i++) {
                        if (i > 0 && i % 3 === 0) {
                            formatted += ' ';
                        }
                        formatted += value[i];
                    }

                    this.phone = formatted;
                }
            }
        }

        // Show success/error messages
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
    @endpush
</x-cabinet-layout>
