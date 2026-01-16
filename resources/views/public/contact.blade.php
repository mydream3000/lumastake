@extends('layouts.public')

@section('content')
    {{-- HERO SECTION --}}
    <section class="relative pt-10 pb-20 overflow-hidden">
        {{-- Background Chart Image --}}
        <div class="absolute inset-0 opacity-40 mix-blend-overlay pointer-events-none">
            <img src="{{ asset('img/contact/contact-hero-bg.png') }}" alt="Chart" class="w-full h-full object-cover">
        </div>

        <div class="max-w-7xl mx-auto px-4 relative z-10 text-center">
            <h1 class="text-6xl md:text-8xl font-black text-[#3B4EFC] mb-6 uppercase">
                Contact Us
            </h1>
            <p class="text-xl md:text-2xl text-[#262262] max-w-3xl mx-auto font-medium leading-relaxed">
                WeвЂ™d love to hear from you! Whether you have a question, feedback, or just want to connect, our team is here to help.
            </p>
        </div>
    </section>

    {{-- CONTACT FORM & INFO SECTION --}}
    <section class="pb-32 bg-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 relative z-10">
            <div class="bg-white border border-[#2BA6FF] rounded-[40px] shadow-[0_4px_30px_rgba(43,166,255,0.15)] overflow-hidden flex flex-col lg:flex-row">

                {{-- Left: Form --}}
                <div class="lg:w-7/12 p-8 md:p-16">
                    <h2 class="text-4xl font-black text-[#3B4EFC] mb-4 uppercase">Get in Touch</h2>
                    <p class="text-xl text-[#262262] mb-12">We are here for you. How we can help?</p>

                    @if(session('status'))
                        <div class="mb-8 p-6 bg-green-50 border border-green-200 rounded-2xl text-green-700 font-medium">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form action="{{ route('contact.send') }}" method="POST" class="space-y-6" x-data="phoneInput()">
                        @csrf
                        <div class="grid md:grid-cols-2 gap-6">
                            {{-- Full Name --}}
                            <div class="space-y-2">
                                <label for="full_name" class="block text-gray-500 font-bold px-2">Full Name</label>
                                <input type="text" id="full_name" name="name" value="{{ old('name') }}" required
                                    class="w-full bg-[#E0F2FF] border border-[#2BA6FF] rounded-xl px-6 py-4 text-lg text-[#262262] focus:outline-none focus:ring-2 focus:ring-[#3B4EFC]/20 transition-all placeholder:text-gray-400"
                                    placeholder="Full Name">
                                @error('name')
                                    <span class="text-red-500 text-sm font-bold px-2">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Phone Number --}}
                            <div class="space-y-2 relative overflow-visible">
                                <label for="phone" class="block text-gray-500 font-bold px-2">Phone Number</label>
                                <div class="flex bg-[#E0F2FF] border border-[#2BA6FF] rounded-xl overflow-visible focus-within:ring-2 focus-within:ring-[#3B4EFC]/20 transition-all">
                                    {{-- Country Selector --}}
                                    <div class="relative">
                                        <button type="button" @click="open = !open"
                                            class="flex items-center gap-2 px-4 h-full border-r border-[#2BA6FF]/30 text-[#262262] font-bold">
                                            <span x-text="selectedCountry.flag"></span>
                                            <span x-text="selectedCountry.phone_code"></span>
                                            <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>

                                        {{-- Dropdown --}}
                                        <div x-show="open" @click.away="open = false"
                                            class="absolute left-0 top-full mt-2 w-72 bg-white border border-[#2BA6FF] rounded-2xl shadow-2xl z-50 max-h-80 overflow-y-auto py-2"
                                            style="display: none;">
                                            <template x-for="country in countries" :key="country.code">
                                                <button type="button" @click="selectCountry(country)"
                                                    class="w-full px-4 py-3 text-left hover:bg-[#E0F2FF] flex items-center gap-3 transition-colors">
                                                    <span x-text="country.flag" class="text-xl"></span>
                                                    <span x-text="country.phone_code" class="font-bold text-[#3B4EFC] min-w-[3rem]"></span>
                                                    <span x-text="country.name" class="text-gray-600 truncate"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>

                                    <input type="tel" x-model="phone" @input="formatPhone" name="phone" maxlength="19"
                                        class="w-full bg-transparent px-4 py-4 text-lg text-[#262262] focus:outline-none placeholder:text-gray-400"
                                        placeholder="Phone Number">
                                </div>
                                <input type="hidden" name="country_code" x-model="selectedCountry.phone_code">
                                <input type="hidden" name="country" x-model="selectedCountry.code">
                                @error('phone')
                                    <span class="text-red-500 text-sm font-bold px-2">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="space-y-2">
                            <label for="email" class="block text-gray-500 font-bold px-2">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                class="w-full bg-[#E0F2FF] border border-[#2BA6FF] rounded-xl px-6 py-4 text-lg text-[#262262] focus:outline-none focus:ring-2 focus:ring-[#3B4EFC]/20 transition-all placeholder:text-gray-400"
                                placeholder="Email">
                            @error('email')
                                <span class="text-red-500 text-sm font-bold px-2">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Message --}}
                        <div class="space-y-2">
                            <label for="message" class="block text-gray-500 font-bold px-2">Message</label>
                            <textarea id="message" name="message" rows="5" required
                                class="w-full bg-[#E0F2FF] border border-[#2BA6FF] rounded-xl px-6 py-4 text-lg text-[#262262] focus:outline-none focus:ring-2 focus:ring-[#3B4EFC]/20 transition-all placeholder:text-gray-400 resize-none"
                                placeholder="Message">{{ old('message') }}</textarea>
                            @error('message')
                                <span class="text-red-500 text-sm font-bold px-2">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Submit Button --}}
                        <div class="pt-4">
                            <button type="submit"
                                class="bg-[#D9FF00] text-[#262262] px-12 py-5 rounded-2xl text-2xl font-black hover:scale-105 transition-transform shadow-lg uppercase">
                                Submit Now
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Right: Contact Info --}}
                <div class="lg:w-5/12 bg-gradient-to-b from-[#3B4EFC] to-[#95D2FF] p-8 md:p-16 text-white relative">
                    <h2 class="text-5xl font-black mb-6 leading-tight uppercase">Contact Information</h2>
                    <p class="text-xl font-medium mb-12 opacity-90">Fill up the form and our team will get back to you within 24 hours.</p>

                    <div class="relative space-y-12">
                        {{-- Vertical Decorative Line --}}
                        <div class="absolute left-[34px] top-4 bottom-4 w-1 bg-[#D9FF00] rounded-full hidden sm:block"></div>

                        {{-- Contact Details --}}
                        <div class="relative flex items-center gap-8">
                            <div class="w-[70px] h-[70px] rounded-full bg-[#D9FF00] flex items-center justify-center flex-shrink-0 z-10 border-4 border-white/20">
                                <i class="fas fa-phone-alt text-[#3B4EFC] text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-lg font-semibold opacity-80 uppercase tracking-wider mb-1">Contact</p>
                                <p class="text-2xl font-black">
                                    <a href="tel:{{ $contactInfo->phone }}">{{ $contactInfo->phone }}</a>
                                </p>
                            </div>
                        </div>

                        <div class="relative flex items-center gap-8">
                            <div class="w-[70px] h-[70px] rounded-full bg-[#D9FF00] flex items-center justify-center flex-shrink-0 z-10 border-4 border-white/20">
                                <i class="fas fa-envelope text-[#3B4EFC] text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-lg font-semibold opacity-80 uppercase tracking-wider mb-1">Email</p>
                                <p class="text-2xl font-black">
                                    <a href="mailto:{{ $contactInfo->email }}">{{ $contactInfo->email }}</a>
                                </p>
                            </div>
                        </div>

                        <div class="relative flex items-center gap-8">
                            <div class="w-[70px] h-[70px] rounded-full bg-[#D9FF00] flex items-center justify-center flex-shrink-0 z-10 border-4 border-white/20">
                                <i class="fas fa-map-marker-alt text-[#3B4EFC] text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-lg font-semibold opacity-80 uppercase tracking-wider mb-1">Location</p>
                                <p class="text-2xl font-black leading-tight">{{ $contactInfo->address }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Decorative Pyramid --}}
                    <div class="absolute right-0 bottom-0 w-48 opacity-10 pointer-events-none">
                        <img src="{{ asset('img/about/about-pyramid-light.png') }}" alt="" class="w-full">
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        function phoneInput() {
            return {
                open: false,
                phone: @json(old('phone', '')),
                countries: [],
                selectedCountry: {code: 'US', name: 'United States', phone_code: '+1', flag: 'рџ‡єрџ‡ё'},

                async init() {
                    try {
                        const response = await fetch('/api/geoip/countries');
                        const data = await response.json();
                        if (data.success) {
                            this.countries = data.countries;
                        } else {
                            // API fallback
                            const resp = await fetch('/api/v1/geoip/countries');
                            const d = await resp.json();
                            if (d.success) this.countries = d.countries;
                        }
                    } catch (error) {
                        console.error('Failed to load countries:', error);
                        // Try v1 as fallback
                        try {
                            const resp = await fetch('/api/v1/geoip/countries');
                            const d = await resp.json();
                            if (d.success) this.countries = d.countries;
                        } catch(e) {}
                    }

                    const oldCountryCode = @json(old('country_code', ''));
                    const oldCountry = @json(old('country', ''));

                    if (oldCountryCode && oldCountry) {
                        const country = this.countries.find(c => c.code === oldCountry && c.phone_code === oldCountryCode);
                        if (country) {
                            this.selectedCountry = country;
                            return;
                        }
                    }

                    try {
                        const response = await fetch('/api/geoip/country');
                        const data = await response.json();
                        if (data.success && data.country) {
                            const country = this.countries.find(c => c.code === data.country.country_code);
                            if (country) {
                                this.selectedCountry = country;
                            }
                        } else {
                             const resp = await fetch('/api/v1/geoip/country');
                             const d = await resp.json();
                             if (d.success && d.country) {
                                const country = this.countries.find(c => c.code === d.country.country_code);
                                if (country) this.selectedCountry = country;
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
                    let value = event.target.value.replace(/\D/g, '');
                    if (value.length > 15) {
                        value = value.slice(0, 15);
                    }
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