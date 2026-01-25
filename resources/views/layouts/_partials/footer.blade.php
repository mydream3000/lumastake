<footer class="bg-[#E0F2FF] w-full relative overflow-hidden pt-24 pb-12">
    {{-- Background Image --}}
{{--    <div class="absolute inset-0 opacity-40 mix-blend-overlay pointer-events-none">--}}
{{--        <img src="{{ asset('img/about/about-hero-bg.png') }}" alt="Background" class="w-full h-full object-cover">--}}
{{--    </div>--}}
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('img/footer_bg.png') }}" alt="Background" class="w-full h-full object-cover">
    </div>

    <div class="max-w-[1280px] mx-auto px-6 relative z-10">
        {{-- CTA AREA --}}
        <div class="flex flex-col lg:flex-row items-center justify-between gap-16 mb-20">
            <div class="max-w-[700px] text-center lg:text-left">
                <img src="{{ asset('img/about/logo-about.png') }}" alt="Luma Stake" class="h-[80px] md:h-[101px] mb-8 mx-auto lg:mx-0">
                <p class="text-2xl md:text-[32px] text-black leading-tight font-poppins">
                    Your future shouldn’t depend on market luck. <br class="hidden md:block">
                    With <span class="font-bold text-[#3B4EFC]">Lumastake</span>, you earn passively, <span class="font-bold">stake confidently</span>, and <span class="font-bold">sleep peacefully.</span>
                </p>
            </div>

            <div class="flex flex-col gap-4 w-full md:w-[350px]">
                <a href="{{ route('register') }}" class="bg-white/20 border border-[#2BA6FF] px-8 py-4 rounded-lg text-xl md:text-[26px] text-black/70 hover:bg-white/40 transition-all text-center font-bold">Get Started Now</a>
                <a href="{{ route('profit-tiers') }}" class="bg-white/20 border border-[#2BA6FF] px-8 py-4 rounded-lg text-xl md:text-[26px] text-black/70 hover:bg-white/40 transition-all text-center font-bold">Explore Plans</a>
                <a href="{{ route('contact') }}" class="bg-white/20 border border-[#2BA6FF] px-8 py-4 rounded-lg text-xl md:text-[26px] text-black/70 hover:bg-white/40 transition-all text-center font-bold">Contact Support</a>
            </div>
        </div>

        {{-- BOTTOM FOOTER --}}
        <div class="pt-12 border-t border-black/10 flex flex-col md:flex-row justify-between items-center gap-8">
            <p class="text-lumastake-navy text-xl font-medium opacity-70">© 2026 Lumastake. All rights reserved.</p>

            <div class="flex space-x-6">
                @foreach($socialLinks as $link)
                    @php
                        $icon = match($link->platform) {
                            'Instagram' => 'img/instagram.svg',
                            'Facebook' => 'img/facebook.svg',
                            'Twitter' => 'img/xlink.svg',
                            'TikTok' => 'img/tiktok.svg',
                            'YouTube' => 'img/youtube.svg',
                            'Telegram' => 'img/telegram.svg',
                            default => null
                        };
                    @endphp
                    @if($icon)
                        <a href="{{ $link->url }}" target="_blank" class="text-lumastake-navy hover:text-lumastake-blue transition-all transform hover:scale-110">
                            <img src="{{ asset($icon) }}" alt="{{ $link->platform }}" class="w-12 h-12 opacity-100 hover:opacity-80">
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</footer>

