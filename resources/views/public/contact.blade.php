@extends('layouts.public')

@section('content')
    {{-- HERO SECTION --}}
    <section class="relative pt-32 pb-20 overflow-hidden bg-white">
        <div class="max-w-[1440px] mx-auto px-4 md:px-12 relative z-10">
            <div class="text-center mb-12">
                <h1 class="text-5xl md:text-[52px] font-the-bold-font font-black text-[#3B4EFC] mb-6 uppercase tracking-tighter leading-[0.9]">
                    Contact Us
                </h1>
                <div class="max-w-[861px] mx-auto">
                    <p class="text-xl md:text-[28px] text-[#262262] leading-tight">
                        We’d love to hear from you! Whether you have a question, feedback, or just want to connect, our team is here to help.
                    </p>
                </div>
            </div>

            {{-- CONTACT CONTAINER --}}
            <div class="bg-white border border-[#2BA6FF] rounded-[13px] shadow-[0_4px_4px_0_rgba(43,166,255,1)] overflow-hidden flex flex-col lg:flex-row min-h-[922px] relative z-20">

                {{-- Left: Form --}}
                <div class="lg:w-[60%] p-8 md:p-14">
                    <h2 class="text-3xl md:text-[40px] font-semibold text-[#3B4EFC] mb-4 leading-tight">Get in Touch</h2>
                    <p class="text-xl md:text-[28px] text-[#262262] mb-12">We are here for you. How we can help?</p>

                    @if(session('status'))
                        <div class="mb-8 p-6 bg-green-50 border border-green-200 rounded-xl text-green-700 font-medium">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form action="{{ route('contact.send') }}" method="POST" class="space-y-8" x-data="phoneInput()">
                        @csrf
                        <div class="grid md:grid-cols-2 gap-8">
                            {{-- Full Name --}}
                            <div class="space-y-2">
                                <label for="full_name" class="block text-[22px] text-black/70 px-2">Full Name</label>
                                <input type="text" id="full_name" name="name" value="{{ old('name') }}" required
                                    class="w-full bg-[#E0F2FF] border border-[#2BA6FF] rounded-[8px] px-6 py-4 text-xl text-[#262262] focus:outline-none transition-all placeholder:text-gray-400 h-[83px]"
                                    placeholder="Full Name">
                                @error('name')
                                    <span class="text-red-500 text-sm font-bold px-2">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Phone Number --}}
                            <div class="space-y-2 relative overflow-visible">
                                <label for="phone" class="block text-[22px] text-black/70 px-2">Phone Number</label>
                                <div class="flex bg-[#E0F2FF] border border-[#2BA6FF] rounded-[8px] overflow-visible transition-all h-[83px]">
                                    {{-- Country Selector --}}
                                    <div class="relative">
                                        <button type="button" @click="open = !open"
                                            class="flex items-center gap-2 px-4 h-full border-r border-[#2BA6FF]/30 text-[#262262] font-bold">
                                            <span :class="selectedCountry.flag_class" class="text-lg"></span>
                                            <span x-text="selectedCountry.phone_code"></span>
                                            <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>

                                        {{-- Dropdown --}}
                                        <div x-show="open" @click.away="open = false"
                                            class="absolute left-0 top-full mt-2 w-72 bg-white border border-[#2BA6FF] rounded-xl shadow-2xl z-50 max-h-80 overflow-y-auto py-2"
                                            style="display: none;">
                                            <template x-for="country in countries" :key="country.code">
                                                <button type="button" @click="selectCountry(country)"
                                                    class="w-full px-4 py-3 text-left hover:bg-[#E0F2FF] flex items-center gap-3 transition-colors">
                                                    <span :class="country.flag_class" class="text-lg"></span>
                                                    <span x-text="country.phone_code" class="font-bold text-[#3B4EFC] min-w-[3rem]"></span>
                                                    <span x-text="country.name" class="text-gray-600 truncate"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>

                                    <input type="tel" x-model="phone" @input="formatPhone" name="phone" maxlength="19"
                                        class="w-full bg-transparent px-4 py-4 text-xl text-[#262262] focus:outline-none placeholder:text-gray-400"
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
                            <label for="email" class="block text-[22px] text-black/70 px-2">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                class="w-full bg-[#E0F2FF] border border-[#2BA6FF] rounded-[8px] px-6 py-4 text-xl text-[#262262] focus:outline-none transition-all placeholder:text-gray-400 h-[83px]"
                                placeholder="Email">
                            @error('email')
                                <span class="text-red-500 text-sm font-bold px-2">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Message --}}
                        <div class="space-y-2">
                            <label for="message" class="block text-[22px] text-black/70 px-2">Message</label>
                            <textarea id="message" name="message" rows="5" required
                                class="w-full bg-[#E0F2FF] border border-[#2BA6FF] rounded-[8px] px-6 py-6 text-xl text-[#262262] focus:outline-none transition-all placeholder:text-gray-400 resize-none min-h-[208px]"
                                placeholder="Message">{{ old('message') }}</textarea>
                            @error('message')
                                <span class="text-red-500 text-sm font-bold px-2">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Submit Button --}}
                        <div class="pt-6">
                            <button type="submit"
                                class="bg-[#3B4EFC] text-white px-10 py-4 rounded-[3px] text-[32px] font-the-bold-font font-black hover:bg-blue-700 transition-all shadow-lg uppercase tracking-tight h-[67px] flex items-center justify-center min-w-[258px]">
                                Submit Now
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Right: Contact Info --}}
                <div class="lg:w-[40%] bg-gradient-to-b from-[#3B4EFC] to-[#95D2FF] p-8 md:p-14 text-white relative flex flex-col m-[15px] rounded-[23px]">
                    <h2 class="text-[45px] font-the-bold-font font-black mb-6 leading-tight uppercase tracking-tighter">Contact Information</h2>
                    <p class="text-[26px] font-normal mb-12 opacity-90">Fill up the form and our team will get back to you within 24 hours.</p>

                    <div class="relative space-y-10 flex-grow py-4">
                        {{-- Vertical Decorative Line --}}
                        <div class="absolute left-[34px] top-0 bottom-0 w-[4px] bg-[#E3FF3B] hidden sm:block"></div>

                        {{-- Contact Details --}}
                        <div class="relative flex items-center gap-8 pl-1">
                            <div class="w-[69px] h-[69px] rounded-full bg-white/20 flex items-center justify-center flex-shrink-0 z-10">
                                <div class="w-[45px] h-[45px] bg-[#E3FF3B] rounded-full"></div>
                            </div>
                            <div>
                                <p class="text-[24px] font-semibold font-manrope opacity-80 uppercase mb-1">Contact</p>
                                <p class="text-[21px] lg:text-[25px] font-bold font-manrope">
                                    <a href="tel:{{ $contactInfo->phone }}">{{ $contactInfo->phone }}</a>
                                </p>
                            </div>
                        </div>

                        <div class="relative flex items-center gap-8 pl-1 lg:mb-12">
                            <div class="w-[69px] h-[69px] rounded-full bg-white/20 flex items-center justify-center flex-shrink-0 z-10">
                                <div class="w-[45px] h-[45px] bg-[#E3FF3B] rounded-full"></div>
                            </div>
                            <div>
                                <p class="text-[24px] font-semibold font-manrope opacity-80 uppercase mb-1">Email</p>
                                <p class="text-[25px] font-bold font-manrope">
                                    <a href="mailto:{{ $contactInfo->email }}">{{ $contactInfo->email }}</a>
                                </p>
                            </div>
                        </div>

                        <div class="relative flex items-start gap-8 pl-1">
                            <div class="w-[69px] h-[69px] rounded-full bg-white/20 flex items-center justify-center flex-shrink-0 z-10">
                                <div class="w-[45px] h-[45px] bg-[#E3FF3B] rounded-full"></div>
                            </div>
                            <div>
                                <p class="text-[24px] font-semibold font-manrope opacity-80 uppercase mb-1">Location</p>
                                <p class="text-[25px] font-bold font-manrope leading-tight">{{ $contactInfo->address }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Decorative Ellipses --}}
        <div class="absolute right-[-200px] top-[10%] w-[600px] h-[600px] bg-[#3B4EFC]/5 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute left-[-200px] top-[20%] w-[600px] h-[600px] bg-[#3B4EFC]/5 rounded-full blur-[120px] pointer-events-none"></div>

        {{-- Background Texture --}}
        <div class="absolute inset-0 opacity-10 pointer-events-none z-0">
            <img src="{{ asset('img/about/about-hero-bg.png') }}" alt="" class="w-full h-full object-cover">
        </div>
    </section>


@endsection
