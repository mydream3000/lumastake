@extends('layouts.public')

@section('content')
    {{-- HERO SECTION --}}
    <section class="relative pt-32 pb-20 overflow-hidden bg-white">
        <div class="max-w-[1440px] mx-auto px-4 md:px-12 relative z-10 text-center">
            <h1 class="text-5xl md:text-[52px] font-the-bold-font font-black text-[#3B4EFC] mb-6 uppercase tracking-tighter leading-[0.9]">
                Frequently Asked Questions
            </h1>
            <div class="max-w-[861px] mx-auto mb-16">
                <p class="text-xl md:text-[28px] text-[#262262] leading-tight">
                    Here are some common questions about Luma Stake
                </p>
            </div>

            {{-- FAQ LIST --}}
            <div class="max-w-[1279px] mx-auto space-y-8 relative z-20 text-left" x-data="{ active: null }">
                @foreach($faqs as $faq)
                    <div class="bg-white border border-[#2BA6FF] rounded-[13px] shadow-[0_4px_4px_0_rgba(43,166,255,1)] overflow-hidden transition-all duration-300">
                        <button
                            @click="active = (active === {{ $faq->id }} ? null : {{ $faq->id }})"
                            class="w-full text-left p-8 md:p-10 flex justify-between items-center focus:outline-none group"
                        >
                            <span class="text-2xl md:text-[36px] font-semibold text-[#262262] pr-12 leading-tight group-hover:text-[#3B4EFC] transition-colors">
                                {{ $faq->question }}
                            </span>
                            <div
                                class="w-[36px] h-[36px] rounded-full bg-gradient-to-b from-[#95D2FF] to-[#3B4EFC] flex items-center justify-center flex-shrink-0 transition-transform duration-500"
                                :class="active === {{ $faq->id }} ? 'rotate-180' : 'rotate-0'"
                            >
                                <svg width="22" height="18" viewBox="0 0 22 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11 17L20.5263 0.5H1.47372L11 17Z" fill="white"/>
                                </svg>
                            </div>
                        </button>

                        <div
                            x-show="active === {{ $faq->id }}"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform -translate-y-4"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-cloak
                        >
                            <div class="px-8 md:px-10 pb-10">
                                <div class="w-full h-[1px] bg-[#2BA6FF]/20 mb-8"></div>
                                <div class="text-xl md:text-[28px] text-black leading-relaxed font-poppins">
                                    {!! nl2br(e($faq->answer)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Decorative background elements --}}
        {{-- Top Left Ellipse --}}
        <div class="absolute left-[-200px] top-[10%] w-[600px] h-[600px] bg-[#3B4EFC]/5 rounded-full blur-[120px] pointer-events-none"></div>
        {{-- Right Ellipse --}}
        <div class="absolute right-[-200px] top-[20%] w-[600px] h-[600px] bg-[#3B4EFC]/5 rounded-full blur-[120px] pointer-events-none"></div>
        {{-- Center blur --}}
        <div class="absolute left-[50%] top-[50%] w-[800px] h-[800px] bg-[#3B4EFC]/3 rounded-full blur-[150px] pointer-events-none"></div>

        {{-- Background Texture --}}
        <div class="absolute inset-0 opacity-10 pointer-events-none z-0">
            <img src="{{ asset('img/faq/faq-hero-bg.png') }}" alt="" class="w-full h-full object-cover">
        </div>
    </section>
@endsection
