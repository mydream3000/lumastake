@extends('layouts.public')

@section('content')
    <section class="relative min-h-screen px-[30px] md:px-[80px] flex items-center pt-20 overflow-hidden ">
        {{-- Background Image --}}
        <div class="absolute inset-0 left-[-16%]">
            <img src="img/about/growth-1.png" alt="Growth Background" class="hidden md:block w-full h-full object-cover object-center">
            <img src="img/about/growth-mob.png" alt="Growth Background" class="block md:hidden w-full h-full object-contain object-bottom">
        </div>

        {{-- Background Overlay --}}
        <div class="absolute inset-0 "></div>

        {{-- Content --}}
        <div class="container-fixed">
            <div class="text-center md:text-left pt-[48px] md:pt-[130px] relative z-10">

            {{-- Main Heading --}}
            <div class="space-y-4">
                <h1 class="font-poppins text-[16px] md:text-4xl lg:text-5xl font-medium text-white">
                A SMARTER WAY TO GROW YOUR
                </h1>
                <h2 class="font-poppins text-4xl md:text-6xl lg:text-9xl font-extrabold gradient-text ">
                    DIGITAL ASSETS
                </h2>
            </div>

            {{-- <div class="flex flex-col lg:flex-row items-center gap-12"> --}}
            {{-- Left Content --}}
            <div class="flex-1 text-center lg:text-left space-y-8 md:max-w-[50%] max-w-full">


                {{-- Description --}}
                <div class="space-y-6 mx-auto lg:mx-0 pt-[22px] md:pt-[129px]">
                    <p class="text-lg md:text-xl text-white/80">
                        At <span class="font-regular text-arbitex-orange">Lumastake</span>, we provide a secure, transparent staking solution for those who value consistency, clarity, and long-term growth.
                    </p>
                    <p class="text-lg md:text-xl text-white/80 pt-[16px] pt-[46px]">
                        In today's fast-changing digital world, we focus on what matters most — protecting capital, delivering steady returns, and making staking simple for everyone, from individual investors to institutions.
                    </p>
                </div>
            </div>

            {{-- Right Content - Decorative Elements --}}
            <div class="flex-1 relative">
                {{-- Decorative Circles --}}
                <div class="relative w-full h-96 lg:h-[500px]">
                    {{-- Large Circle green --}}
                    <div class="absolute top-[-120%] left-1/3 transform -translate-x-1/2 w-[457px] h-[457px] lg:w-[457px] lg:h-[457px] bg-[#00FFA37D] rounded-full blur-[140px]"></div>
                    {{-- Medium Circle shadow --}}
                    <div class="hidden md:block absolute bottom-[-120px] right-[24px] w-48 h-48 lg:w-[600px] lg:h-80 bg-[#101221] rounded-full blur-[60px]"></div>
                    {{-- Small Circle orange --}}
                    <div class="hidden md:block absolute top-[-110%] right-[-10%] w-32 h-32 lg:w-[400px] lg:h-[400px] bg-[#FF451C96] rounded-full blur-[100px]"></div>
                    {{-- mobile Circle orange --}}
                    <div class="md:hidden absolute bottom-0 left-2/3 transform -translate-x-1/2 w-[357px] h-[357px] bg-[#FF451C96] rounded-full blur-[120px] z-10"></div>
                    {{-- little Circle shadow mobile --}}
                    <div class=" md:hidden absolute bottom-[-45px] right-10 w-[300px] h-[100px] bg-[#101221] rounded-full blur-[30px] z-10"></div>

                </div>
            </div>
            {{-- </div> --}}
        </div>
        </div>
    </section>

    {{-- Purpose, Mission, Vision Section --}}
    <section class=" relative px-[16px] md:px-[32px] md:px-0 md:border-b border-[#D9D9D96B]">
        <div class="container-fixed">
            {{-- Grid 2x3 for 6 blocks --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 ">
                {{-- Row 1 --}}
                {{-- Block 1: SVG with background (left) --}}
                <div class="hidden md:block relative h-96 lg:h-[500px] flex items-center justify-center">
                    {{-- <div class="absolute flex items-center justify-center top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-32 h-32 lg:w-[400px] lg:h-[400px] bg-arbitex-orange/15 rounded-full blur-xl"></div> --}}
                    <div class="absolute inset-0">
                        <img src="img/about/svg/shadow_purpose.svg" alt="Vision Icon" class="w-full h-full object-contain">
                    </div>

                    <div class="absolute inset-1 flex items-center justify-center">
                        <img src="img/about/svg/PURPOSE.svg" alt="Purpose Icon" class="max-w-[257px] max-h-[257px] object-contain">
                    </div>
                </div>

                {{-- Block 2: OUR PURPOSE text (right) --}}
                <div class="rounded-3xl md:rounded-none bg-arbitex-orange mb-[32px] md:mb-0 px-[11px] pt-[11px] pb-[24px] md:p-12 flex flex-col justify-center relative overflow-hidden min-h-auto lg:h-[500px]">
                    <div class="relative z-10 text-center md:text-left">
                        <div class="md:hidden flex items-center justify-center pt-[24px] pb-[16px]">
                            <img src="img/about/svg/PURPOSE.svg" alt="Purpose Icon" class="max-w-[59px] max-h-[59px] object-contain color-white">
                        </div>
                        <h3 class="text-3xl md:text-4xl lg:text-5xl font-audiowide text-white md:mb-6">
                            OUR PURPOSE
                        </h3>
                        <p class="text-sm md:text-xl text-white/90 pt-[16px] md:pt-[32px]">
                            Our platform was built to bridge the gap between complex blockchain technologies and practical wealth generation. By leveraging advanced staking models and real-time automation, Lumastake allows users to benefit from stable and efficient earnings without the need for speculation or constant market monitoring.
                        </p>
                    </div>
                </div>

                {{-- Row 2 --}}
                {{-- Block 3: OUR MISSION text (left) --}}
                <div class="rounded-3xl md:rounded-none bg-arbitex-pink mb-[32px] md:mb-0 px-[42px] pb-[44px] md:p-12 flex flex-col justify-center relative overflow-hidden min-h-auto lg:h-[500px]">
                    <div class="relative z-10 text-center md:text-left">
                        <div class="md:hidden flex items-center justify-center pt-[34px] pb-[21px]">
                            <img src="img/about/svg/MISSION.svg" alt="Mission Icon" class="max-w-[59px] max-h-[59px] object-contain">
                        </div>
                        <h3 class="text-3xl md:text-4xl lg:text-5xl font-audiowide text-white md:mb-6">
                            OUR MISSION
                        </h3>
                        <p class="text-sm md:text-xl text-white/90 pt-[16px] md:pt-[32px]">
                            To provide a secure and structured environment where users can stake with confidence, earn passively, and grow their portfolios in a sustainable way.
                        </p>
                    </div>
                </div>

                {{-- Block 4: SVG with background (right) --}}
                <div class="hidden md:block relative h-96 lg:h-[500px] flex items-center justify-center">
                    <div class="absolute inset-0">
                        <img src="img/about/svg/shadow_mission.svg" alt="Mission Icon" class="w-full h-full object-contain">
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <img src="img/about/svg/MISSION.svg" alt="Mission Icon" class="max-w-[257px] max-h-[257px] object-contain">
                    </div>
                </div>

                {{-- Row 3 --}}
                {{-- Block 5: SVG with background (left) --}}
                <div class="hidden md:block relative h-96 lg:h-[500px] flex items-center justify-center">
                    <div class="absolute inset-0">
                        <img src="img/about/svg/shadow_vision.svg" alt="Vision Icon" class="w-full h-full object-contain">
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <img src="img/about/svg/VISION.svg" alt="Vision Icon" class="max-w-[257px] max-h-[257px] object-contain">
                    </div>
                </div>

                {{-- Block 6: OUR VISION text (right) --}}
                <div class="rounded-3xl md:rounded-none bg-arbitex-green mb-[32px] md:mb-0 px-[42px] pb-[44px] md:p-12 flex flex-col justify-center relative overflow-hidden min-h-auto lg:h-[500px]">
                    <div class="relative z-10 text-center md:text-left">
                        <div class="md:hidden flex items-center justify-center pt-[27px] pb-[27px]">
                            <img src="img/about/svg/VISION.svg" alt="Vision Icon" class="max-w-[59px] max-h-[59px] object-contain">
                        </div>
                        <h3 class="text-3xl md:text-4xl lg:text-5xl font-audiowide text-white mb-6">
                            OUR VISION
                        </h3>
                        <p class="text-lg md:text-xl text-white/90">
                            To become a global leader in decentralized wealth solutions by consistently delivering results, building trust, and prioritizing user experience.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Why Lumastake Section rgba(255, 0, 216, 0.16) --}}
    <section class="py-[18px] md:py-[146px] relative">
        <div class="container-fixed">
            {{-- Header with background and border --}}
            <div class="relative mb-16">
                <div class="bg-[#FF00D829] backdrop-blur-sm rounded-3xl p-8 md:p-12 border-4 border-arbitex-pink relative">
                    <h2 class="text-4xl md:text-6xl lg:text-8xl font-audiowide text-white text-center">
                        <span class="text-white">WHY </span>
                        <span class="text-arbitex-pink">LUMASTAKE</span>
                    </h2>
                </div>
            </div>

            {{-- Content with vertical line and circles --}}
            <div class="relative">
                {{-- Main vertical line (aligned with left border of header and centered through circles) --}}
                <div class="absolute left-0 top-[-18%] md:top-[-32%] lg:top-[-36%] bottom-0 w-[1px] bg-arbitex-pink"></div>

                {{-- Content blocks with precise positioning --}}
                <div class="ml-12 md:ml-20 space-y-20">
                    {{-- Predictable Returns --}}
                    <div class="relative flex items-start gap-8">
                        {{-- Circle on line --}}
                        <div class="absolute left-[-60px] md:left-[-105px] top-[2px] w-[26px] h-[26px] md:w-[49px] md:h-[49px] bg-[#FF00D829] rounded-full ">
                            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[16px] h-[16px] md:w-[32px] md:h-[32px]  bg-arbitex-pink rounded-full">
                            </div>

                        </div>

                        {{-- Text content --}}
                        <div class="flex-1">
                            <h3 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-arbitex-pink mb-4">
                                PREDICTABLE RETURNS
                            </h3>
                            <p class="text-lg md:text-xl text-white/80">
                                Our staking system is designed for performance, not promises. Earnings are based on clear terms and transparent structures.
                            </p>
                        </div>
                    </div>

                    {{-- Capital Protection --}}
                    <div class="relative flex items-start gap-8">
                        {{-- Circle on line (using SVG from Figma) --}}
                        <div class="absolute left-[-60px] md:left-[-105px] top-[2px] w-[25px] h-[25px] md:w-[49px] md:h-[49px] bg-[#FF00D829] rounded-full ">
                        </div>

                        {{-- Text content --}}
                        <div class="flex-1">
                            <h3 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-white mb-4">
                                CAPITAL PROTECTION
                            </h3>
                            <p class="text-lg md:text-xl text-white/80">
                                Security is our foundation. We follow strict protocols and maintain a risk-managed approach to staking.
                            </p>
                        </div>
                    </div>

                    {{-- User-Centric Design --}}
                    <div class="relative flex items-start gap-8">
                        {{-- Circle on line (using SVG from Figma) --}}
                        <div class="absolute left-[-60px] md:left-[-105px] top-[2px] w-[25px] h-[25px] md:w-[49px] md:h-[49px] bg-[#FF00D829] rounded-full ">
                        </div>

                        {{-- Text content --}}
                        <div class="flex-1">
                            <h3 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-white mb-4">
                                USER-CENTRIC DESIGN
                            </h3>
                            <p class="text-lg md:text-xl text-white/80 pb-[18px]">
                                The Lumastake interface is built to remove confusion. With easy onboarding, straightforward tiers, and live earnings tracking, users stay in control at every step.
                            </p>
                        </div>
                        <div class="absolute left-[-62px] md:left-[-112px] bottom-[-8px] md:-bottom-4 md:w-[63px] md:h-[30px] w-[30px] h-[15px] bg-[#FF00D8] md:bg-[#FF00D829] " style="clip-path: ellipse(40% 8% at 50% 50%)"></div>
                        {{-- Large Circle --}}
                        <div class="absolute bottom-[-150%] left-[-40px] transform -translate-x-1/2 w-[292px] h-[292px] rounded-full bg-[#E104954F]  blur-[130px]"></div>
                        <div class="absolute bottom-[-150px] left-2/3 transform -translate-x-1/2 w-[192px] h-[192px] rounded-full bg-[#2323DE]  blur-[160px]"></div>

                    </div>


                </div>
            </div>
        </div>
    </section>

    {{-- How We Generate Profits Section --}}
    <section class="pt-[65px] md:pt-0">
        <div class="gradient-bg relative pt-[20px] pb-[11px] md:py-20 ">
            <div class="mx-auto">
                <div class="text-center">
                    <h2 class="text-[20px] md:text-6xl lg:text-7xl font-audiowide text-white md:mb-6">
                        HOW WE GENERATE <span class="text-[40px] md:text-6xl lg:text-7xl">PROFITS</span>
                    </h2>
                </div>
            </div>
        </div>
        <div class="container-fixed text-center py-[38px] md:py-[80px]">
            <p class="text-[16px] md:text-xl lg:text-2xl text-white/90 leading-relaxed">
                We are a professional <span class="font-semibold text-arbitex-orange">arbitrage firm.</span>
                Staked funds are used in low-risk arbitrage trades that profit from price differences across platforms.
                This method works in any market up or down — as it relies on <span class="font-semibold text-arbitex-green">volatility,</span>
                not direction. Profits are shared between Lumastake and our users, powered by a team of experts with
                <span class="font-semibold text-arbitex-pink">10+ years of experience.</span>
            </p>
        </div>

    </section>

    {{-- Who We Serve Section --}}
    <section class="container-fixed pt-[36px] md:pt-[100px]">
        <div class=" mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-[86px]">
                <div class="relative">
                    <h2 class="text-[94px] md:text-8xl lg:text-9xl font-audiowide text-white/10 absolute inset-0 flex items-center justify-center">
                        SERVE
                    </h2>
                    <div class="relative z-10">
                        <h3 class="text-[30px] md:text-5xl lg:text-6xl font-audiowide text-white mb-6">
                            WHO WE SERVE
                        </h3>
                    </div>
                </div>
            </div>

            <div class="border border-[#D9D9D96B] rounded-3xl p-8 md:p-12 text-center relative">
                <div class="w-full h-full bg-[#22253B80] blur-[1px] absolute inset-0 rounded-3xl"></div>
                <div class="absolute inset-0 bg-[#00FFA3] w-[12px] h-[60%] top-[22%] -left-[12px] rounded-2xl"></div>
                <p class="text-lg md:text-xl lg:text-2xl text-white/80 mx-auto">
                    Lumastake is trusted by working professionals, crypto enthusiasts, passive income seekers, and anyone who wants their capital to work harder — without unnecessary risk.
                </p>
            </div>
        </div>
    </section>

    {{-- Welcome to Lumastake Section --}}
    <section class="container-fixed py-[58px] md:py-[190px] relative">
        <div class="">
            <div class="space-y-8">
                <h3 class="hidden md:block text-2xl md:text-3xl lg:text-4xl font-extrabold text-white tracking-wider">
                    Built with Integrity.<br>
                    Driven by Performance.
                </h3>
                <h2 class="mx-auto text-center md:text-left text-[24px] md:text-6xl lg:text-8xl font-audiowide gradient-text">
                    WELCOME TO <span class="text-[46px] md:text-6xl lg:text-8xl">LUMASTAKE</span>
                </h2>
                <h3 class=" md:hidden text-center text-[20px] font-extrabold text-white tracking-wider">
                    Built with Integrity.<br>
                    Driven by Performance.
                </h3>

            </div>
        </div>
    </section>
@endsection
