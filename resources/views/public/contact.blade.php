@extends('layouts.public')

@section('content')
    <div class="Background_image_at_bottom min-h-screen pt-20 md:pt-32 pb-16 relative overflow-hidden">
        <!-- Background image at bottom -->
        {{--    <div class="absolute bottom-0 left-0 right-0 h-[400px] md:h-[600px] pointer-events-none z-0">--}}
        {{--        <img src="{{ asset('images/figma/contact-bg.png') }}" alt="" class="w-full h-full object-cover opacity-30">--}}
        {{--    </div>--}}

        <!-- Background decorative gradients (desktop only) -->
        <div class="absolute inset-0 pointer-events-none hidden md:block">
            <div class="absolute top-20 right-0 w-[500px] h-[500px] bg-gradient-radial from-arbitex-pink/30 via-arbitex-orange/20 to-transparent rounded-full blur-3xl"></div>
            <div class="absolute top-40 left-0 w-[400px] h-[400px] bg-gradient-radial from-arbitex-green/20 to-transparent rounded-full blur-3xl"></div>
        </div>

        <div class="mx-auto max-w-[1440px] px-5 md:px-8 lg:px-12 relative z-10">
            <!-- Header Section -->
            <div class="text-left mb-8 md:mb-12">
                <h1 class="text-4xl md:text-6xl lg:text-8xl font-extrabold mb-6 gradient-text bg-clip-text text-transparent">
                    CONTACT US
                </h1>
                <p class="text-base md:text-lg lg:text-xl text-white/90 max-w-3xl leading-relaxed">
                    We'd love to hear from you! Whether you have a question, feedback, or just want to connect, our team
                    is here to help.
                </p>
            </div>

            <!-- Main Content Grid -->
            <section class="contact-section">
                <div class="contact-panel">
                    <div class="flex flex-col md:flex-row gap-8 md:gap-12 lg:gap-16 relative z-[2]">
                        <!-- Contact Form -->
                        <div class="w-full md:w-1/2 order-1 overflow-visible">
                            <div class="form-contact bg-[#1a1d2e]/80 backdrop-blur-sm border border-white/10 rounded-2xl md:rounded-3xl p-6 md:p-8 mx-auto max-w-full overflow-visible">
                                <div class="mb-6 md:mb-8">
                                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-arbitex-orange mb-2 md:mb-3">
                                        Get in Touch</h2>
                                    <p class="text-sm md:text-base text-white/80">We are here for you. How we can
                                        help?</p>
                                </div>

                                @if(session('status'))
                                    <div class="mb-6 p-4 bg-arbitex-green/20 border border-arbitex-green rounded-lg text-white text-sm">
                                        {{ session('status') }}
                                    </div>
                                @endif

                                <form method="POST"
                                      action="{{ route('contact.send') }}"
                                      class="space-y-5 md:space-y-6">
                                    @csrf
                                    <!-- Full Name Field -->
                                    <div>
                                        <label for="full_name"
                                               class="block text-sm md:text-base font-medium text-white/90 mb-2">Full
                                            Name</label>
                                        <input type="text"
                                               id="full_name"
                                               name="name"
                                               value="{{ old('name') }}"
                                               required
                                               class="w-full px-0 py-2.5 md:py-3 bg-transparent border-0 border-b border-white/30 text-base md:text-lg text-white/70 placeholder-white/40 focus:border-white/60 focus:outline-none transition-colors @error('name') border-red-500 @enderror"
                                               placeholder="Your Full name">
                                        @error('name')
                                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Email Field -->
                                    <div>
                                        <label for="email"
                                               class="block text-sm md:text-base font-medium text-white/90 mb-2">Email</label>
                                        <input type="email"
                                               id="email"
                                               name="email"
                                               value="{{ old('email') }}"
                                               required
                                               class="w-full px-0 py-2.5 md:py-3 bg-transparent border-0 border-b border-white/30 text-base md:text-lg text-white/70 placeholder-white/40 focus:border-white/60 focus:outline-none transition-colors @error('email') border-red-500 @enderror"
                                               placeholder="lumastake@gmail.com">
                                        @error('email')
                                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Phone Number Field -->
                                    <div class="relative overflow-visible"
                                         x-data="phoneInput()">
                                        <label for="phone"
                                               class="block text-sm md:text-base font-medium text-white/90 mb-2">Phone
                                            Number</label>

                                        <!-- Phone input with country selector -->
                                        <div class="flex w-full items-stretch border-b border-white/30 focus-within:border-white/60 transition-colors @error('phone') border-red-500 @enderror">
                                            <!-- Country selector button -->
                                            <button
                                                type="button"
                                                @click="open = !open"
                                                class="px-2 md:px-3 py-2.5 md:py-3 bg-transparent text-white/80 flex items-center gap-1.5 md:gap-2 hover:text-white transition-colors"
                                            >
                                                <span x-text="selectedCountry.flag"
                                                      class="text-lg md:text-xl"></span>
                                                <span x-text="selectedCountry.phone_code"
                                                      class="text-sm md:text-base font-medium"></span>
                                                <svg class="w-3 h-3 md:w-4 md:h-4"
                                                     fill="none"
                                                     stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round"
                                                          stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>

                                            <!-- Phone input -->
                                            <input
                                                type="tel"
                                                x-model="phone"
                                                @input="formatPhone"
                                                name="phone"
                                                maxlength="19"
                                                class="flex-1 px-0 py-2.5 md:py-3 bg-transparent border-0 text-base md:text-lg text-white/70 placeholder-white/40 focus:outline-none"
                                                placeholder="123 456 7890"
                                            >
                                        </div>

                                        <!-- Countries dropdown -->
                                        <div
                                            x-show="open"
                                            @click.away="open = false"
                                            class="absolute left-0 right-0 z-50 mt-1 bg-[#1a1d2e] border border-white/20 rounded-md shadow-xl max-h-60 md:max-h-80 overflow-y-auto"
                                            style="display:none;"
                                        >
                                            <template x-for="country in countries"
                                                      :key="country.code">
                                                <button
                                                    type="button"
                                                    @click="selectCountry(country)"
                                                    class="w-full px-3 md:px-4 py-2.5 md:py-3 text-left hover:bg-white/10 flex items-center gap-2 md:gap-3 transition-colors"
                                                >
                                                    <span class="text-lg md:text-xl"
                                                          x-text="country.flag"></span>
                                                    <span class="text-sm md:text-base font-medium text-white"
                                                          x-text="country.phone_code"></span>
                                                    <span class="text-sm md:text-base text-white/60"
                                                          x-text="country.name"></span>
                                                </button>
                                            </template>
                                        </div>

                                        <!-- Hidden fields for form submission -->
                                        <input type="hidden"
                                               name="country_code"
                                               x-model="selectedCountry.phone_code">
                                        <input type="hidden"
                                               name="country"
                                               x-model="selectedCountry.code">

                                        @error('phone')
                                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Message Field -->
                                    <div>
                                        <label for="message"
                                               class="block text-sm md:text-base font-medium text-white/90 mb-2">Message</label>
                                        <textarea id="message"
                                                  name="message"
                                                  rows="4"
                                                  required
                                                  class="w-full px-3 md:px-4 py-3 md:py-4 bg-transparent border border-white/30 rounded-md text-base md:text-lg text-white/70 placeholder-white/40 focus:border-white/60 focus:outline-none transition-colors resize-none @error('message') border-red-500 @enderror"
                                                  placeholder="How can we help you?">{{ old('message') }}</textarea>
                                        @error('message')
                                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="pt-2">
                                        <button type="submit"
                                                class="bg-arbitex-orange hover:bg-arbitex-orange/90 text-white font-semibold text-base md:text-lg px-8 md:px-10 py-3 md:py-3.5 rounded-lg transition-all duration-300">
                                            Submit Now
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="w-full md:w-1/2 order-2">
                            <div class="md:pl-4 lg:pl-8">
                                <div class="mb-8 md:mb-10 hidden md:block">
                                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-white mb-3 md:mb-4">
                                        Contact Information</h2>
                                    <p class="text-sm md:text-base text-white/80 leading-relaxed">
                                        Fill up the form and our team will get back to you within 24 hours.
                                    </p>
                                </div>

                                <!-- Contact Details with Decorative Line -->
                                <div class="relative timeline">
                                    <div class="space-y-8 md:space-y-10 timeline__content">
                                    @if($contactInfo->phone)
                                            <!-- Contact Phone -->
                                            <div class="flex items-start gap-4 md:gap-6">
                                                <div class="relative flex-shrink-0">
                                                    <span class="timeline__dot"></span>
                                                </div>
                                                <div>
                                                    <h3 class="text-base md:text-lg font-semibold text-white/90 mb-1 md:mb-2">
                                                        Contact</h3>
                                                    <p class="text-lg md:text-xl lg:text-2xl font-bold text-white">
                                                        <a href="tel:{{ $contactInfo->phone }}">{{ $contactInfo->phone }}</a>
                                                    </p>
                                                </div>
                                            </div>
                                        @endif

                                        @if($contactInfo->email)
                                            <!-- Email -->
                                            <div class="flex items-start gap-4 md:gap-6">
                                                <div class="relative flex-shrink-0">
                                                    <span class="timeline__dot"></span>
                                                </div>
                                                <div>
                                                    <h3 class="text-base md:text-lg font-semibold text-white/90 mb-1 md:mb-2">
                                                        Email</h3>
                                                    <p class="text-lg md:text-xl lg:text-2xl font-bold text-white">
                                                        <a href="mailto:{{ $contactInfo->email }}">{{ $contactInfo->email }}</a>
                                                    </p>
                                                </div>
                                            </div>
                                        @endif

                                        @if($contactInfo->address)
                                            <!-- Location -->
                                            <div class="flex items-start gap-4 md:gap-6">
                                                <div class="relative flex-shrink-0">
                                                    <span class="timeline__dot"></span>
                                                </div>
                                                <div>
                                                    <h3 class="text-base md:text-lg font-semibold text-white/90 mb-1 md:mb-2">
                                                        Location</h3>
                                                    <p class="text-lg md:text-xl lg:text-2xl font-bold text-white">
                                                        <a href="https://maps.app.goo.gl/95dRHPUJJJJ8zHQh7"
                                                           target="_blank">{{ $contactInfo->address }}</a>
                                                    </p>
                                                </div>
                                            </div>
                                        @endif

                                        @if($contactInfo->telegram)
                                            <!-- Telegram -->
                                            <div class="flex items-start gap-4 md:gap-6">
                                                <div class="relative flex-shrink-0">
                                                    <span class="timeline__dot"></span>
                                                </div>
{{--                                                <div>--}}
{{--                                                    <h3 class="text-base md:text-lg font-semibold text-white/90 mb-1 md:mb-2">--}}
{{--                                                        Telegram</h3>--}}
{{--                                                    <p class="text-lg md:text-xl lg:text-2xl font-bold text-white">--}}
{{--                                                        <a href="https://t.me/{{ str_replace('@', '', $contactInfo->telegram) }}"--}}
{{--                                                           target="_blank">{{ $contactInfo->telegram }}</a>--}}
{{--                                                    </p>--}}
{{--                                                </div>--}}
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="contact-bg"
                         aria-hidden="true">
                        <img src="{{ asset('images/figma/contact-bg.png') }}"
                             alt="">
                    </div>
                </div>

            </section>
        </div>
    </div>

    <style>
        .bg-gradient-radial {
            background: radial-gradient(circle, currentColor, transparent);
        }
    </style>

    @push('scripts')
        <script>
            function phoneInput() {
                return {
                    open: false,
                    phone: '{{ old('phone', '') }}',
                    countries: [],
                    selectedCountry: {code: 'US', name: 'United States', phone_code: '+1', flag: '🇺🇸'},

                    async init() {
                        // Load all countries
                        try {
                            const response = await fetch('/api/v1/geoip/countries');
                            const data = await response.json();
                            if (data.success) {
                                this.countries = data.countries;
                            }
                        } catch (error) {
                            console.error('Failed to load countries:', error);
                        }

                        // Restore old values if validation failed
                        const oldCountryCode = '{{ old('country_code', '') }}';
                        const oldCountry = '{{ old('country', '') }}';

                        if (oldCountryCode && oldCountry) {
                            // Find and set old country
                            const country = this.countries.find(c => c.code === oldCountry && c.phone_code === oldCountryCode);
                            if (country) {
                                this.selectedCountry = country;
                                return; // Don't detect by IP if we have old values
                            }
                        }

                        // Get country by IP
                        try {
                            const response = await fetch('/api/v1/geoip/country');
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
        </script>
    @endpush

@endsection
