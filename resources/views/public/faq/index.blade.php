@extends('layouts.public')

@section('content')
    <!-- Hero Section -->
    <section class="relative pt-16 pb-24 overflow-hidden">
        {{-- Background Chart Image --}}
        <div class="absolute inset-0 opacity-40 mix-blend-overlay pointer-events-none">
            <img src="{{ asset('img/faq/faq-hero-bg.png') }}" alt="Chart" class="w-full h-full object-cover">
        </div>

        <div class="max-w-7xl mx-auto px-4 relative z-10 text-center">
            <h1 class="text-5xl md:text-7xl font-black text-[#3B4EFC] mb-6 uppercase">
                Frequently Asked Questions
            </h1>
            <p class="text-xl md:text-2xl text-[#262262] max-w-3xl mx-auto font-medium">
                Here are some common questions about Luma Stake
            </p>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="pb-32 bg-white relative overflow-hidden">
        {{-- Decorative background elements --}}
        <div class="absolute top-0 right-0 w-1/3 opacity-5 pointer-events-none">
            <img src="{{ asset('img/about/about-pyramid-light.png') }}" alt="" class="w-full">
        </div>

        <div class="max-w-5xl mx-auto px-4 relative z-10">
            <div class="space-y-6" id="faq-accordion">
                @foreach($faqs as $faq)
                    <div class="faq-item group">
                        <button class="faq-trigger w-full text-left bg-white border border-[#2BA6FF] rounded-2xl p-6 md:p-8 flex justify-between items-center transition-all duration-300 hover:shadow-[0_4px_15px_rgba(43,166,255,0.15)] focus:outline-none"
                                aria-expanded="false">
                            <span class="text-xl md:text-2xl font-bold text-[#262262] pr-8 leading-tight">
                                {{ $faq->question }}
                            </span>
                            <div class="faq-icon flex-shrink-0 w-10 h-10 border-2 border-[#3B4EFC] rounded-full flex items-center justify-center transition-transform duration-500 text-[#3B4EFC]">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </button>
                        <div class="faq-content overflow-hidden transition-all duration-500 max-h-0 bg-blue-50/30 rounded-b-2xl -mt-4 border-x border-b border-[#2BA6FF]/30">
                            <div class="p-8 pt-12 text-lg text-gray-700 leading-relaxed">
                                {!! nl2br(e($faq->answer)) !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- FINAL CTA SECTION --}}
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="bg-[#E0F2FF] rounded-[40px] p-16 flex flex-col md:flex-row items-center justify-between gap-12 relative overflow-hidden">
                <div class="md:w-2/3 relative z-10">
                    <h2 class="text-5xl md:text-7xl font-black text-[#262262] leading-none mb-8 uppercase">
                        STILL HAVE <br> <span class="text-[#3B4EFC]">QUESTIONS?</span>
                    </h2>
                    <p class="text-2xl text-gray-700 leading-relaxed font-medium">
                        Our support team is available 24/7 to help you with any inquiries regarding our staking platform.
                    </p>
                </div>
                <div class="md:w-1/3 flex justify-center relative z-10">
                    <a href="{{ route('contact') }}" class="bg-[#D9FF00] text-[#262262] text-3xl font-black px-12 py-6 rounded-2xl hover:scale-105 transition-transform shadow-xl uppercase text-center">
                        Contact Us
                    </a>
                </div>

                {{-- Decorative background element --}}
                <div class="absolute right-0 bottom-0 opacity-10 pointer-events-none">
                    <img src="{{ asset('img/about/about-pyramid-light.png') }}" alt="" class="w-96">
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const faqItems = document.querySelectorAll('.faq-item');

            faqItems.forEach(item => {
                const trigger = item.querySelector('.faq-trigger');
                const content = item.querySelector('.faq-content');
                const icon = item.querySelector('.faq-icon');

                trigger.addEventListener('click', () => {
                    const isOpen = trigger.getAttribute('aria-expanded') === 'true';

                    // Close all other items (optional, but usually better UX)
                    faqItems.forEach(otherItem => {
                        if (otherItem !== item) {
                            const otherTrigger = otherItem.querySelector('.faq-trigger');
                            const otherContent = otherItem.querySelector('.faq-content');
                            const otherIcon = otherItem.querySelector('.faq-icon');

                            otherTrigger.setAttribute('aria-expanded', 'false');
                            otherContent.style.maxHeight = '0';
                            otherIcon.style.transform = 'rotate(0deg)';
                            otherTrigger.classList.remove('rounded-b-none', 'border-b-0');
                        }
                    });

                    // Toggle current item
                    if (isOpen) {
                        trigger.setAttribute('aria-expanded', 'false');
                        content.style.maxHeight = '0';
                        icon.style.transform = 'rotate(0deg)';
                        // Wait for animation to finish before removing classes if needed
                        setTimeout(() => {
                           if (trigger.getAttribute('aria-expanded') === 'false') {
                               trigger.classList.remove('rounded-b-none', 'border-b-0');
                           }
                        }, 300);
                    } else {
                        trigger.setAttribute('aria-expanded', 'true');
                        trigger.classList.add('rounded-b-none', 'border-b-0');
                        content.style.maxHeight = content.scrollHeight + 'px';
                        icon.style.transform = 'rotate(180deg)';
                    }
                });
            });
        });
    </script>
    @endpush
@endsection