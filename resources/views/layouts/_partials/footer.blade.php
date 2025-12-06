<section class="footer relative">

    <div class="absolute inset-0 gradient-bg"></div>

    <div class="relative mx-auto max-w-[1440px] px-6 pb-12 grid gap-10 lg:grid-cols-12 items-start">

        <div class="lg:col-span-5 flex flex-col items-center lg:items-start">

            <div class="inline-block bg-[#101221] px-6 py-4 rounded-b-[28px] shadow-[0_10px_30px_rgba(0,0,0,.25)]">
                <a class="logo logo-mob" href="{{ route('home') }}">
                    <img class="logo logo-mob mb-4" src="{{ asset('assets/af0ff98938fcb741aa993c0252fc8fbc16f782bd.png') }}"
                         alt="Lumastake">
                </a>
            </div>


            <div class="mt-6 flex gap-4 justify-center lg:justify-start">
                @php
                    $socialIcons = [
                        'Instagram' => 'img/instagram.svg',
                        'Facebook' => 'img/facebook.svg',
                        'Twitter' => 'img/xlink.svg',
                        'TikTok' => 'img/tiktok.svg',
                        'YouTube' => 'img/youtube-white-icon.svg',
                        'Telegram' => 'img/telegram-logo.svg'
                    ];
                @endphp

                @foreach($socialLinks ?? [] as $link)
                    @php
                        $icon = $socialIcons[$link->platform] ?? null;
                    @endphp
                    @if($icon && $link->url)
                        <a href="{{ $link->url }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="size-12 md:size-14 rounded-[12px] ring-1 ring-white/30 shadow-md flex items-center justify-center hover:-translate-y-0.5 transition">
                            <img src="{{ asset($icon) }}"
                                 alt="{{ $link->platform }}"
                                 class="w-6 h-6 md:w-7 md:h-7">
                        </a>
                    @endif
                @endforeach
            </div>
        </div>


        <div class="lg:col-span-7 lg:pt-14 flex flex-col items-center lg:items-start">
            <p class="text-white/90 text-xl md:text-2xl lg:text-[32px] leading-snug max-w-[760px] text-center lg:text-left">
                Your future shouldn't depend on market luck. With Lumastake,
                <b class="text-white">you earn passively</b>, <b class="text-white">stake confidently</b>,
                and <b class="text-white">sleep peacefully</b>.
            </p>

            <div class="mt-6 flex flex-col md:flex-row flex-wrap gap-4 justify-center lg:justify-start w-full md:w-auto">
                <a class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 px-6 py-3 text-lg md:text-xl text-white hover:bg-white/10"
                   href="{{url('/register') }}">Get Started Now</a>
                <a class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 px-6 py-3 text-lg md:text-xl text-white hover:bg-white/10"
                   href="{{url('/tiers') }}">Explore Plans</a>
                <a class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 px-6 py-3 text-lg md:text-xl text-white hover:bg-white/10"
                   href="{{ url('/contact') }}">Contact Support</a>
            </div>
        </div>


        <div class="lg:col-span-12 text-center lg:text-left">
            <p class="mt-6 text-white/90 text-lg md:text-xl lg:text-2xl">© 2025 All rights reserved.</p>
        </div>
    </div>
</section>
