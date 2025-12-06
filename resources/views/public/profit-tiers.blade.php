@extends('layouts.public')

@section('content')
    <style>
        .gradient-text {
            background: linear-gradient(90deg, #00ffa3 0%, #ff00d8 48.558%, #ff451c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .gradient-bg {
            background: linear-gradient(90deg, #00ffa3 0%, #ff00d8 51.923%, #ff451c 100%);
        }

        .gradient-bg-vertical {
            background: linear-gradient(180deg, #00ffa3 0%, #ff00d8 53.846%, #ff451c 100%);
        }

        .glass-effect {
            background: rgba(34, 37, 59, 0.5);
            backdrop-filter: blur(15px);
        }

        .glass-effect-light {
            background: rgba(217, 217, 217, 0.06);
        }

        /* Responsive styles for Main Section */
        @media (max-width: 1400px) {
            .main-left-card {
                left: 5% !important;
                width: 42% !important;
            }

            .main-right-card {
                left: 52% !important;
                width: 42% !important;
            }
        }

        @media (max-width: 1024px) {
            .main-left-card {
                position: relative !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                margin: 0 auto 40px !important;
                max-width: 591px !important;
            }

            .main-right-card {
                position: relative !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                margin: 0 auto !important;
                max-width: 591px !important;
            }

            .main-section-wrapper {
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
                justify-items: ;
                align-items: center !important;
                padding: 40px 20px !important;
            }


        }

        @media (max-width: 768px) {

            .main-left-card,
            .main-right-card {
                height: auto !important;
                min-height: 280px !important;
            }

            .main-left-card .card-title-52,
            .main-right-card .card-title-52 {
                font-size: 38px !important;
            }

            .main-left-card .card-title-70,
            .main-right-card .card-title-70 {
                font-size: 52px !important;
            }

            .main-left-card .card-description,
            .main-right-card .card-description {
                font-size: 22px !important;
            }
        }

        @media (max-width: 480px) {

            .main-left-card,
            .main-right-card {
                padding: 30px 24px !important;
                min-height: 240px !important;
            }

            .main-left-card .card-title-52,
            .main-right-card .card-title-52 {
                font-size: 28px !important;
            }

            .main-left-card .card-title-70,
            .main-right-card .card-title-70 {
                font-size: 38px !important;
            }

            .main-left-card .card-description,
            .main-right-card .card-description {
                font-size: 18px !important;
                max-width: 100% !important;
            }
        }

        /* Content Section Styles */
        .content-section {
            min-height: 2200px;
        }

        .content-bg-image {
            position: absolute;
            left: -321px;
            top: 365px;
            width: 1952px;
            height: 1160px;
            background: radial-gradient(ellipse at 50% 30%, rgba(255, 0, 216, 0.15) 0%, rgba(255, 69, 28, 0.1) 40%, transparent 70%);
            pointer-events: none;
            z-index: 1;
            filter: blur(80px);
        }

        .content-circle-1 {
            position: absolute;
            left: 62%;
            top: 131px;
            width: 234px;
            height: 197px;
            opacity: 0.3;
            pointer-events: none;
            z-index: 1;
            overflow: hidden;
        }

        .content-circle-1::before {
            content: '';
            position: absolute;
            width: 500%;
            height: 500%;
            margin-left: -200%;
            margin-top: -200%;
            background: radial-gradient(circle, rgba(255, 0, 216, 0.4) 0%, rgba(255, 69, 28, 0.2) 50%, transparent 70%);
            border-radius: 50%;
        }

        .content-circle-2 {
            position: absolute;
            left: 38%;
            top: 83px;
            width: 407px;
            height: 400px;
            opacity: 0.3;
            pointer-events: none;
            z-index: 1;
            overflow: hidden;
        }

        .content-circle-2::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            margin-left: -50%;
            margin-top: -50%;
            background: radial-gradient(circle, rgba(0, 255, 163, 0.3) 0%, rgba(255, 0, 216, 0.2) 50%, transparent 70%);
            border-radius: 50%;
        }

        .content-circle-3 {
            position: absolute;
            left: 20%;
            top: 145px;
            width: 407px;
            height: 400px;
            opacity: 0.3;
            pointer-events: none;
            z-index: 1;
            overflow: hidden;
        }

        .content-circle-3::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            margin-left: -50%;
            margin-top: -50%;
            background: radial-gradient(circle, rgba(0, 255, 163, 0.3) 0%, rgba(255, 0, 216, 0.2) 50%, transparent 70%);
            border-radius: 50%;
        }

        .content-circle-bottom {
            position: absolute;
            left: 32%;
            top: 1684px;
            width: 473px;
            height: 473px;
            opacity: 0.3;
            pointer-events: none;
            z-index: 1;
            overflow: hidden;
        }

        .content-circle-bottom::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            margin-left: -63%;
            margin-top: -63%;
            background: radial-gradient(circle, rgba(255, 69, 28, 0.4) 0%, rgba(255, 0, 216, 0.2) 50%, transparent 70%);
            border-radius: 50%;
        }

        /* Responsive styles for Content Section */
        @media (max-width: 1024px) {
            .content-section {
                min-height: auto;
                padding-top: 40px;
                padding-bottom: 40px;
            }

            .content-bg-image,
            .content-circle-1,
            .content-circle-2,
            .content-circle-3,
            .content-circle-bottom {
                display: none;
            }

            .tier-card {
                width: 100%;
                margin-left: 0;
                margin-right: 0;
                padding-left: 32px;
                padding-right: 32px;
            }

            /* .tier-card .absolute {
            display: none !important;
            } */
            /* .content-section .flex-wrap {
            gap: 2rem !important;
            } */
        }

        @media (max-width: 768px) {
            .tier-card {
                padding: 24px;
                padding-left: 24px;
                padding-right: 24px;
            }

            .tier-card h3 {
                font-size: 32px !important;
            }

            .tier-card p {
                font-size: 20px !important;
            }

            .tier-card .space-y-\[8px\] {
                font-size: 14px !important;
            }
        }

        @media (max-width: 480px) {
            .tier-card {
                padding: 45px 20px 20px 20px;
            }

            .tier-card h3 {
                font-size: 28px !important;
            }

            .tier-card p {
                font-size: 18px !important;
            }

            .tier-card .h-\[39px\] {
                height: 32px !important;
            }

            .tier-card .space-y-\[8px\] {
                font-size: 14px !important;
            }
        }
    </style>

    <section class="content-section pt-20 pb-[100px] lg:pb-[338px] relative overflow-hidden">


        {{-- start -------------------------------------------------------------------------------------------------------------------------- --}}
        <div class="relative mx-auto pb-[129px]"
             style=" min-height: 666px">
            {{-- Background decorative circles --}}
            <div class="absolute pointer-events-none"
                 style="left: 1083px; top: 260px; width: 406px; height: 406px; opacity: 0.3; z-index: 0;">
                <div style="width: 100%; height: 100%; transform: scale(2.739); transform-origin: center;">
                    <img src="assets/630ae6568f79e53581d752492d091b1828abdea6.svg"
                         alt=""
                         style="width: 100%; height: 100%;">
                </div>
            </div>
            <div class="absolute pointer-events-none"
                 style="left: -194px; top: 0; width: 548px; height: 548px; opacity: 0.3; z-index: 0;">
                <div style="width: 100%; height: 100%; transform: scale(2.547); transform-origin: center;">
                    <img src="assets/a3b39ab3a19404e65d126c6546db93de41be2df5.svg"
                         alt=""
                         style="width: 100%; height: 100%;">
                </div>
            </div>

            {{-- Content Container --}}
            <div class="md:flex flex-col items-center justify-center py-[40px] px-[20px] relative">
                <div class="relative md:flex justify-center w-[1280px] max-w-full">
                    {{-- Left Card: Rise Through Every Stake --}}
                    <div
                        class="main-left-card absolute md:pr-[20px] left-[-70px] top-[78px] w-[591px] h-[328px] z-[10]">
                        <div
                            class="bg-[rgba(34,37,59,0.5)] px-[20px] py-[48px] rounded-[31px] border border-[rgba(217,217,217,0.42)] w-full h-full relative">
                            <div class="flex flex-col items-center justify-center h-full ">
                                <h1 class="font-bold text-center"
                                    style="margin-bottom: 32px; line-height: 0;">
                                    <a href="{{ route('profit-tiers') }}"
                                       id="normal">
                                    <div class="text-[30px] lg:text-[52px] leading-[0.94] text-[#ff00d8] mb-[8px]">
                                        Normal
                                    </div>
                                    <div class="text-[30px] lg:text-[70px] leading-[0.94] text-[#ff00d8]">
                                        Account
                                    </div>
                                    </a>
                                </h1>
                                <p
                                    class="card-description text-white text-center text-[28px] leading-normal max-w-[460px] m-0">
                                    Fixed Profit Structure
                                </p>
                            </div>
                        </div>
                        {{-- Pink accent line --}}
                        <div
                            class="main-accent-line-pink absolute bg-[#ff00d8] left-[25%] md:left-[22%] lg:left-[25%] md:bottom-[-17px]  w-[50%] lg:w-[298px] h-[17px] lg:h-[17px] z-[15]">
                        </div>
                    </div>

                    {{-- Right Card: Dynamic Earning Tiers --}}
                    <div class="main-right-card absolute left-[756px] top-[216px] w-[591px] h-[328px] z-[10] ">
                        {{-- Orange accent line --}}
                        <div
                            class="hidden md:block main-accent-line-orange absolute bg-[#ff451c] left-[25%] md:left-[22%] lg:left-[25%]  md:bottom-[-17px]  w-[50%] lg:w-[298px] h-[17px] lg:h-[17px] z-[15]">
                        </div>
                        <div
                            class="bg-[rgba(34,37,59,0.5)] px-[20px] py-[48px] rounded-[31px] border border-[rgba(217,217,217,0.42)] w-full h-full relative">
                            <div class="flex flex-col items-center justify-center h-full">
                                <h1 class="font-bold text-center"
                                    style="margin-bottom: 32px; line-height: 0;">
                                    <a href="{{ route('profit-tiers', ['type' => 'islamic']) }}"
                                       id="islamic">
                                    <div class="text-[30px] lg:text-[52px] leading-[0.94] text-[#ff451c] mb-[8px]">
                                        Islamic
                                    </div>
                                    <div class="text-[30px] lg:text-[70px] leading-[0.94] text-[#ff451c]">
                                        Account
                                    </div>
                                    </a>
                                </h1>
                                    <p
                                        class="card-description text-white text-center text-[28px] leading-normal max-w-[460px] m-0">
                                        Faith Meets Finance – The Halal Way
                                    </p>
                            </div>
                        </div>
                        {{-- Orange accent line for mobile --}}
                        <div
                            class="md:hidden main-accent-line-orange absolute bg-[#ff451c] left-[25%] md:left-[22%] lg:left-[25%]  md:bottom-[-17px]  w-[50%] lg:w-[298px] h-[17px] lg:h-[17px] z-[15]">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- end -------------------------------------------------------------------------------------------------------------------------- --}}

        <div class="flex justify-center mb-12 gap-4">
            <a href="{{ route('profit-tiers') }}"
               id="normal"
               class="px-8 py-3 rounded-md text-lg font-semibold transition-colors {{ $accountType === 'normal' ? 'bg-arbitex-orange text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }}">
                Normal Account
            </a>
            <a href="{{ route('profit-tiers', ['type' => 'islamic']) }}"
               id="islamic"
               class="px-8 py-3 rounded-md text-lg font-semibold transition-colors {{ $accountType === 'islamic' ? 'bg-arbitex-orange text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }}">
                Islamic Account
            </a>
        </div>


        <div class=" mx-auto  relative">
            {{-- Background decorative image --}}
            <div class="content-bg-image"></div>

            {{-- Background decorative circles --}}
            <div
                class="hidden md:block absolute top-[-100px] left-1/3 transform -translate-x-2/2 w-48 h-48 lg:w-[473px] lg:h-[473px] bg-[#F6BD0E38] rounded-full blur-[100px]">
            </div>

            <div
                class="hidden md:block absolute bottom-[780px] left-80 transform -translate-x-2/2 w-48 h-48 lg:w-[173px] lg:h-[173px] bg-[#2F7DEA] rounded-full blur-[80px]">
            </div>

            <div
                class="hidden md:block absolute bottom-[-120px] left-1/2 transform -translate-x-1/2 w-48 h-48 lg:w-[473px] lg:h-[473px] bg-[#E104954F] rounded-4xl blur-[100px]">
            </div>


            {{-- Main flex container for tiers --}}
            <div
                class="relative flex flex-col lg:flex-row flex-wrap gap-y-[44px] md:gap-y-[44px] lg:gap-y-[54px]  z-10">

                @if($accountType === 'normal')
                    {{-- STARTER --}}
                    @php $tier1 = $tiers->where('level', 1)->first(); @endphp
                    <div
                        class="relative tier-card pl-[70px] lg:mb-[180px] bg-[rgba(34,37,59,0.5)] border-y-2 pb-[12px] border-[rgba(217,217,217,0.42)] h-full w-full lg:w-[calc(50%-92px)] pl-[56px] pr-[56px] pt-[24px] ">
                        <h3 class="tiers_name_1 font-extrabold text-[40px] text-[#f6bd0e]">{{ strtoupper($tier1->name) }}</h3>
                        <div class="h-[39px] w-[250px] border border-[#ff00d8] flex items-center justify-start pl-[4px]">
                            <p class="range_usd_1 font-extrabold text-[24px] text-[#ff00d8]">
                                @if($tier1->max_balance)
                                    ${{ number_format($tier1->min_balance, 0) }} –
                                    ${{ number_format($tier1->max_balance, 0) }} USD
                                @else
                                    ${{ number_format($tier1->min_balance, 0) }}+ USD
                                @endif
                            </p>
                        </div>
                        <div class="space-y-[8px] text-[16px] text-neutral-100 pt-[24px]">
                            @foreach($tier1->percentages as $index => $percentage)
                                <div class="flex justify-between"
                                     style="max-width: 324px;"><span class="days_{{ $index + 1 }} font-semibold"
                                                                     style="width: 109px;">{{ $percentage->days }} DAYS</span><span class="percent_{{ $index + 1 }}"
                                                                                                                                    style="width: 52px;">{{ rtrim(rtrim(number_format($percentage->percentage, 1), '0'), '.') }}%</span>
                                </div>
                            @endforeach
                        </div>
                        {{-- Central tiers image --}}
                        <div
                            class="absolute left-0 top-0 md:left-auto md:right-0 md:top-1/2 transform -translate-y-1/2 w-[103px] h-[103px] md:w-[203px] md:h-[203px]      lg:right-[-206px] lg:bottom-[-52px] px-4 py-4  lg:w-[402px] lg:h-[402px] pointer-events-none z-20">
                            <img src="img/profit_tiers/t1.svg"
                                 alt=""
                                 class="tier-01 w-[100%] h-[100%] object-cover rotate-[0deg]">
                            <div
                                class="absolute right-[34px] top-[34px] md:right-[60px] md:top-[110px] md:w-[87px] md:h-[45px] text-[22px] md:text-[67px]       lg:right-[142px] lg:top-[190px] lg:w-[117px] lg:h-[95px] font-extrabold lg:text-[100px] text-white tracking-[5px] z-30 flex items-end justify-center">
                                01
                            </div>
                        </div>
                    </div>

                    {{-- BRONZE --}}
                    @php $tier2 = $tiers->where('level', 2)->first(); @endphp
                    <div
                        class="relative pl-[70px] lg:mt-[180px] tier-card bg-[rgba(34,37,59,0.5)] border-y-2 pb-[12px] border-[rgba(217,217,217,0.42)] w-full lg:w-[calc(50%-92px)] lg:pl-[210px] pr-[56px] pt-[24px] lg:ml-auto">
                        <h3 class="tiers_name_2 font-extrabold text-[40px] text-[#ff613e]">{{ strtoupper($tier2->name) }}</h3>
                        <div class="h-[39px] w-[283px] border border-[#ff00d8] flex items-center justify-start pl-[4px]">
                            <p class="range_usd_2 font-extrabold text-[24px] text-[#ff00d8]">
                                @if($tier2->max_balance)
                                    ${{ number_format($tier2->min_balance, 0) }} –
                                    ${{ number_format($tier2->max_balance, 0) }} USD
                                @else
                                    ${{ number_format($tier2->min_balance, 0) }}+ USD
                                @endif
                            </p>
                        </div>
                        <div class="space-y-[8px] text-[16px] text-neutral-100 pt-[24px]">
                            @foreach($tier2->percentages as $index => $percentage)
                                <div class="flex justify-between"
                                     style="max-width: 324px;"><span class="days_{{ $index + 1 }} font-semibold"
                                                                     style="width: 109px;">{{ $percentage->days }} DAYS</span><span class="percent_{{ $index + 1 }}"
                                                                                                                                    style="width: 52px;">{{ rtrim(rtrim(number_format($percentage->percentage, 1), '0'), '.') }}%</span>
                                </div>
                            @endforeach
                        </div>
                        {{-- Central tiers image --}}
                        <div class="absolute left-[70%] top-0 md:left-auto md:right-0 md:top-1/2 transform -translate-y-1/2 lg:translate-y-0 w-[103px] h-[103px] md:w-[203px] md:h-[203px]   lg:left-[-199.5px] lg:top-[-47.5px] px-4 py-4 lg:w-[402px] lg:h-[402px] pointer-events-none z-20">
                            <img src="img/profit_tiers/t2.svg"
                                 alt=""
                                 class="w-[100%] h-[100%] object-cover">
                            <div class="absolute right-[31px] top-[34px]
                md:right-[60px] md:top-[110px] md:w-[87px] md:h-[45px]
                text-[22px] md:text-[67px]
                lg:right-[142px] lg:top-[190px] lg:w-[117px] lg:h-[95px]
                font-extrabold lg:text-[100px] text-white tracking-[5px]
                z-30 flex items-end justify-center">
                                02
                            </div>
                        </div>

                    </div>

                    {{-- SILVER --}}
                    @php $tier3 = $tiers->where('level', 3)->first(); @endphp
                    <div
                        class="relative pl-[70px] lg:mb-[180px] lg:mt-[-180px] tier-card bg-[rgba(34,37,59,0.5)] border-y-2 pb-[12px] border-[rgba(217,217,217,0.42)] w-full lg:w-[calc(50%-92px)] pl-[56px] pr-[56px] pt-[24px]">
                        <h3 class="tiers_name_3 font-extrabold text-[40px] text-[#f70808]">{{ strtoupper($tier3->name) }}</h3>
                        <div class="h-[39px] w-[283px] border border-[#ff00d8] flex items-center justify-start pl-[4px]">
                            <p class="range_usd_3 font-extrabold text-[24px] text-[#ff00d8]">
                                @if($tier3->max_balance)
                                    ${{ number_format($tier3->min_balance, 0) }} –
                                    ${{ number_format($tier3->max_balance, 0) }} USD
                                @else
                                    ${{ number_format($tier3->min_balance, 0) }}+ USD
                                @endif
                            </p>
                        </div>
                        <div class="space-y-[8px] text-[16px] text-neutral-100 pt-[24px]">
                            @foreach($tier3->percentages as $index => $percentage)
                                <div class="flex justify-between"
                                     style="max-width: 324px;"><span class="days_{{ $index + 1 }} font-semibold"
                                                                     style="width: 109px;">{{ $percentage->days }} DAYS</span><span class="percent_{{ $index + 1 }}"
                                                                                                                                    style="width: 52px;">{{ rtrim(rtrim(number_format($percentage->percentage, 1), '0'), '.') }}%</span>
                                </div>
                            @endforeach
                        </div>
                        {{-- Central tiers image --}}
                        <div
                            class="absolute left-0 top-0 md:left-auto md:right-0 md:top-1/2 transform -translate-y-1/2 lg:translate-y-0 w-[103px] h-[103px] md:w-[203px] md:h-[203px] lg:right-[-202px] lg:top-[-52px] px-4 py-4 lg:w-[402px] lg:h-[402px] pointer-events-none z-20">
                            <img src="img/profit_tiers/t3.svg"
                                 alt=""
                                 class="w-[100%] h-[100%] object-cover">
                            <div
                                class="absolute right-[31px] top-[34px] md:right-[60px] md:top-[110px] md:w-[87px] md:h-[45px] text-[22px] md:text-[67px] lg:right-[142px] lg:top-[190px] lg:w-[117px] lg:h-[95px] font-extrabold lg:text-[100px] text-white tracking-[5px] z-30 flex items-end justify-center">
                                03
                            </div>
                        </div>
                    </div>


                    {{-- GOLD --}}
                    @php $tier4 = $tiers->where('level', 4)->first(); @endphp
                    <div
                        class="relative pl-[70px] tier-card bg-[rgba(34,37,59,0.5)] border-y-2 pb-[12px] border-[rgba(217,217,217,0.42)] w-full lg:w-[calc(50%-92px)] lg:pl-[210px] pr-[56px] pt-[24px] lg:ml-auto">
                        <h3 class="tiers_name_4 font-extrabold text-[40px] text-[#e10495]">{{ strtoupper($tier4->name) }}</h3>
                        <div class="h-[39px] w-[330px] border border-[#ff00d8] flex items-center justify-start pl-[4px]">
                            <p class="range_usd_4 font-extrabold text-[24px] text-[#ff00d8]">
                                @if($tier4->max_balance)
                                    ${{ number_format($tier4->min_balance, 0) }} –
                                    ${{ number_format($tier4->max_balance, 0) }} USD
                                @else
                                    ${{ number_format($tier4->min_balance, 0) }}+ USD
                                @endif
                            </p>
                        </div>
                        <div class="space-y-[8px] text-[16px] text-neutral-100 pt-[24px]">
                            @foreach($tier4->percentages as $index => $percentage)
                                <div class="flex justify-between"
                                     style="max-width: 324px;"><span class="days_{{ $index + 1 }} font-semibold"
                                                                     style="width: 109px;">{{ $percentage->days }} DAYS</span><span class="percent_{{ $index + 1 }}"
                                                                                                                                    style="width: 52px;">{{ rtrim(rtrim(number_format($percentage->percentage, 1), '0'), '.') }}%</span>
                                </div>
                            @endforeach
                        </div>
                        {{-- Central tiers image --}}
                        <div
                            class="absolute left-[70%] top-0 md:left-auto md:right-0 md:top-1/2 transform -translate-y-1/2 lg:translate-y-0 w-[103px] h-[103px] md:w-[203px] md:h-[203px]   lg:left-[-199.5px] lg:top-[-47.5px] px-4 py-4 lg:w-[402px] lg:h-[402px] pointer-events-none z-20">
                            <img src="img/profit_tiers/t4.svg"
                                 alt=""
                                 class="w-[100%] h-[100%] object-cover">
                            <div
                                class="absolute right-[30px] top-[35px] md:right-[60px] md:top-[110px] md:w-[87px] md:h-[45px] text-[22px] md:text-[67px] lg:right-[142px] lg:top-[190px] lg:w-[117px] lg:h-[95px] font-extrabold lg:text-[100px] text-white tracking-[5px] z-30 flex items-end justify-center">
                                04
                            </div>
                        </div>
                    </div>


                    {{-- PLATINUM --}}
                    @php $tier5 = $tiers->where('level', 5)->first(); @endphp
                    <div
                        class="relative pl-[70px] lg:mb-[180px] lg:mt-[-180px] tier-card bg-[rgba(34,37,59,0.5)] border-y-2 pb-[12px] border-[rgba(217,217,217,0.42)] w-full lg:w-[calc(50%-92px)] pl-[56px] pr-[56px] pt-[24px]">
                        <h3 class="tiers_name_5 font-extrabold text-[40px] text-[#c000ca]">{{ strtoupper($tier5->name) }}</h3>
                        <div class="h-[39px] w-[340px] border border-[#ff00d8] flex items-center justify-start pl-[4px]">
                            <p class="range_usd_5 font-extrabold text-[24px] text-[#ff00d8]">
                                @if($tier5->max_balance)
                                    ${{ number_format($tier5->min_balance, 0) }} –
                                    ${{ number_format($tier5->max_balance, 0) }} USD
                                @else
                                    ${{ number_format($tier5->min_balance, 0) }}+ USD
                                @endif
                            </p>
                        </div>
                        <div class="space-y-[8px] text-[16px] text-neutral-100 pt-[24px]">
                            @foreach($tier5->percentages as $index => $percentage)
                                <div class="flex justify-between"
                                     style="max-width: 324px;"><span class="days_{{ $index + 1 }} font-semibold"
                                                                     style="width: 109px;">{{ $percentage->days }} DAYS</span><span class="percent_{{ $index + 1 }}"
                                                                                                                                    style="width: 52px;">{{ rtrim(rtrim(number_format($percentage->percentage, 1), '0'), '.') }}%</span>
                                </div>
                            @endforeach
                        </div>
                        {{-- Central tiers image --}}
                        <div
                            class="absolute left-0 top-0 md:left-auto md:right-0 md:top-1/2 transform -translate-y-1/2 lg:translate-y-0 w-[103px] h-[103px] md:w-[203px] md:h-[203px] lg:right-[-202px] lg:top-[-52px] px-4 py-4 lg:w-[402px] lg:h-[402px] pointer-events-none z-20">
                            <img src="img/profit_tiers/t5.svg"
                                 alt=""
                                 class="w-[100%] h-[100%] object-cover">
                            <div
                                class="absolute right-[31px] top-[34px] md:right-[60px] md:top-[110px] md:w-[87px] md:h-[45px] text-[22px] md:text-[67px] lg:right-[142px] lg:top-[190px] lg:w-[117px] lg:h-[95px] font-extrabold lg:text-[100px] text-white tracking-[5px] z-30 flex items-end justify-center">
                                05
                            </div>
                        </div>
                    </div>


                    {{-- TITANIUM --}}
                    @php $tier6 = $tiers->where('level', 6)->first(); @endphp
                    <div
                        class="relative pl-[70px] tier-card bg-[rgba(34,37,59,0.5)] border-y-2 pb-[12px] border-[rgba(217,217,217,0.42)] w-full lg:w-[calc(50%-92px)] lg:pl-[210px] pr-[56px] pt-[24px] lg:ml-auto">
                        <h3 class="tiers_name_6 font-extrabold text-[40px] text-[#2f7dea]">{{ strtoupper($tier6->name) }}</h3>
                        <div class="h-[39px] w-[340px] border border-[#ff00d8] flex items-center justify-start pl-[4px]">
                            <p class="range_usd_6 font-extrabold text-[24px] text-[#ff00d8]">
                                @if($tier6->max_balance)
                                    ${{ number_format($tier6->min_balance, 0) }} –
                                    ${{ number_format($tier6->max_balance, 0) }} USD
                                @else
                                    ${{ number_format($tier6->min_balance, 0) }}+ USD
                                @endif
                            </p>
                        </div>
                        <div class="space-y-[8px] text-[16px] text-neutral-100 pt-[24px]">
                            @foreach($tier6->percentages as $index => $percentage)
                                <div class="flex justify-between"
                                     style="max-width: 324px;"><span class="days_{{ $index + 1 }} font-semibold"
                                                                     style="width: 109px;">{{ $percentage->days }} DAYS</span><span class="percent_{{ $index + 1 }}"
                                                                                                                                    style="width: 52px;">{{ rtrim(rtrim(number_format($percentage->percentage, 1), '0'), '.') }}%</span>
                                </div>
                            @endforeach
                        </div>
                        {{-- Central tiers image --}}
                        <div
                            class="absolute left-[70%] top-0 md:left-auto md:right-0 md:top-1/2 transform -translate-y-1/2 lg:translate-y-0 w-[103px] h-[103px] md:w-[203px] md:h-[203px] lg:left-[-199px] lg:top-[-48px] px-4 py-4 lg:w-[402px] lg:h-[402px] pointer-events-none z-20">
                            <img src="img/profit_tiers/t6.svg"
                                 alt=""
                                 class="w-[100%] h-[100%] object-cover">
                            <div
                                class="absolute right-[31px] top-[34px] md:right-[60px] md:top-[110px] md:w-[87px] md:h-[45px] text-[22px] md:text-[67px] lg:right-[142px] lg:top-[190px] lg:w-[117px] lg:h-[95px] font-extrabold lg:text-[100px] text-white tracking-[5px] z-30 flex items-end justify-center">
                                06
                            </div>
                        </div>
                    </div>

                    {{-- DIAMOND --}}
                    @php $tier7 = $tiers->where('level', 7)->first(); @endphp
                    <div
                        class="relative pl-[70px] lg:mb-[180px] lg:mt-[-180px] tier-card bg-[rgba(34,37,59,0.5)] border-y-2 pb-[12px] border-[rgba(217,217,217,0.42)] w-full lg:w-[calc(50%-92px)] pl-[56px] pr-[56px] pt-[24px]">
                        <h3 class="tiers_name_7 font-extrabold text-[40px] text-[#1393af]">{{ strtoupper($tier7->name) }}</h3>
                        <div class="h-[39px] w-[340px] border border-[#ff00d8] flex items-center justify-start pl-[4px]">
                            <p class="range_usd_7 font-extrabold text-[24px] text-[#ff00d8]">
                                @if($tier7->max_balance)
                                    ${{ number_format($tier7->min_balance, 0) }} –
                                    ${{ number_format($tier7->max_balance, 0) }} USD
                                @else
                                    ${{ number_format($tier7->min_balance, 0) }}+ USD
                                @endif
                            </p>
                        </div>
                        <div class="space-y-[8px] text-[16px] text-neutral-100 pt-[24px]">
                            @foreach($tier7->percentages as $index => $percentage)
                                <div class="flex justify-between"
                                     style="max-width: 324px;"><span class="days_{{ $index + 1 }} font-semibold"
                                                                     style="width: 109px;">{{ $percentage->days }} DAYS</span><span class="percent_{{ $index + 1 }}"
                                                                                                                                    style="width: 52px;">{{ rtrim(rtrim(number_format($percentage->percentage, 1), '0'), '.') }}%</span>
                                </div>
                            @endforeach
                        </div>


                        <!-- Central tiers image -->
                        <div
                            class="absolute left-0 top-0 md:left-auto md:right-0 md:top-1/2 transform -translate-y-1/2 lg:translate-y-0 w-[103px] h-[103px] md:w-[203px] md:h-[203px] lg:right-[-202px] lg:top-[-52px] px-4 py-4 lg:w-[402px] lg:h-[402px] pointer-events-none z-20">
                            <img src="img/profit_tiers/t7.svg"
                                 alt=""
                                 class="w-[100%] h-[100%] object-cover">
                            <div
                                class="absolute right-[31px] top-[34px] md:right-[60px] md:top-[110px] md:w-[87px] md:h-[45px] text-[22px] md:text-[67px] lg:right-[142px] lg:top-[190px] lg:w-[117px] lg:h-[95px] font-extrabold lg:text-[100px] text-white tracking-[5px] z-30 flex items-end justify-center">
                                07
                            </div>
                        </div>
                    </div>


                    {{-- ELITE DIAMOND --}}
                    @php $tier8 = $tiers->where('level', 8)->first(); @endphp
                    <div
                        class="relative pl-[70px] tier-card bg-[rgba(34,37,59,0.5)] border-y-2 pb-[12px] border-[rgba(217,217,217,0.42)] w-full lg:w-[calc(50%-92px)] lg:pl-[210px] pr-[56px] pt-[24px] lg:ml-auto">
                        <h3 class="tiers_name_8 font-extrabold text-[40px] text-[#29bdb4]">{{ strtoupper($tier8->name) }}</h3>
                        <div class="h-[39px] w-[340px] border border-[#ff00d8] flex items-center justify-start pl-[4px]">
                            <p class="range_usd_8 font-extrabold text-[24px] text-[#ff00d8]">
                                @if($tier8->max_balance)
                                    ${{ number_format($tier8->min_balance, 0) }} –
                                    ${{ number_format($tier8->max_balance, 0) }} USD
                                @else
                                    ${{ number_format($tier8->min_balance, 0) }}+ USD
                                @endif
                            </p>
                        </div>
                        <div class="space-y-[8px] text-[16px] text-neutral-100 pt-[24px]">
                            @foreach($tier8->percentages as $index => $percentage)
                                <div class="flex justify-between"
                                     style="max-width: 324px;"><span class="days_{{ $index + 1 }} font-semibold"
                                                                     style="width: 109px;">{{ $percentage->days }} DAYS</span><span class="percent_{{ $index + 1 }}"
                                                                                                                                    style="width: 52px;">{{ rtrim(rtrim(number_format($percentage->percentage, 1), '0'), '.') }}%</span>
                                </div>
                            @endforeach
                        </div>
                        {{-- Central tiers image --}}
                        <div
                            class="absolute left-[70%] top-0 md:left-auto md:right-0 md:top-1/2 transform -translate-y-1/2 lg:translate-y-0 w-[103px] h-[103px] md:w-[203px] md:h-[203px] lg:left-[-199px] lg:top-[-48px] px-4 py-4 lg:w-[402px] lg:h-[402px] pointer-events-none z-20">
                            <img src="img/profit_tiers/t8.svg"
                                 alt=""
                                 class="w-[100%] h-[100%] object-cover">
                            <div
                                class="absolute right-[31px] top-[34px] md:right-[60px] md:top-[110px] md:w-[87px] md:h-[45px] text-[22px] md:text-[67px] lg:right-[142px] lg:top-[190px] lg:w-[117px] lg:h-[95px] font-extrabold lg:text-[100px] text-white tracking-[5px] z-30 flex items-end justify-center">
                                08
                            </div>
                        </div>
                    </div>



                    {{-- CROWN ELITE --}}
                    @php $tier9 = $tiers->where('level', 9)->first(); @endphp
                    <div
                        class="relative pl-[70px] lg:mb-[180px] lg:mt-[-180px] tier-card bg-[rgba(34,37,59,0.5)] border-y-2 pb-[12px] border-[rgba(217,217,217,0.42)] w-full lg:w-[calc(50%-92px)] pl-[56px] pr-[56px] pt-[24px]">
                        <h3 class="tiers_name_9 font-extrabold text-[40px] text-[#82ca8a]">{{ strtoupper($tier9->name) }}</h3>
                        <div class="h-[39px] w-[340px] border border-[#ff00d8] flex items-center justify-start pl-[4px]">
                            <p class="range_usd_9 font-extrabold text-[24px] text-[#ff00d8]">
                                @if($tier9->max_balance)
                                    ${{ number_format($tier9->min_balance, 0) }} –
                                    ${{ number_format($tier9->max_balance, 0) }} USD
                                @else
                                    ${{ number_format($tier9->min_balance, 0) }}+ USD
                                @endif
                            </p>
                        </div>
                        <div class="space-y-[8px] text-[16px] text-neutral-100 pt-[24px]">
                            @foreach($tier9->percentages as $index => $percentage)
                                <div class="flex justify-between"
                                     style="max-width: 324px;"><span class="days_{{ $index + 1 }} font-semibold"
                                                                     style="width: 109px;">{{ $percentage->days }} DAYS</span><span class="percent_{{ $index + 1 }}"
                                                                                                                                    style="width: 52px;">{{ rtrim(rtrim(number_format($percentage->percentage, 1), '0'), '.') }}%</span>
                                </div>
                            @endforeach
                        </div>
                        {{-- Central tiers image --}}
                        <div
                            class="absolute left-0 top-0 md:left-auto md:right-0 md:top-1/2 transform -translate-y-1/2 lg:translate-y-0 w-[103px] h-[103px] md:w-[203px] md:h-[203px] lg:right-[-202px] lg:top-[-52px] px-4 py-4 lg:w-[402px] lg:h-[402px] pointer-events-none z-20">
                            <img src="img/profit_tiers/t9.svg"
                                 alt=""
                                 class="w-[100%] h-[100%] object-cover">
                            <div
                                class="absolute right-[31px] top-[34px] md:right-[60px] md:top-[110px] md:w-[87px] md:h-[45px] text-[22px] md:text-[67px] lg:right-[142px] lg:top-[190px] lg:w-[117px] lg:h-[95px] font-extrabold lg:text-[100px] text-white tracking-[5px] z-30 flex items-end justify-center">
                                09
                            </div>
                        </div>
                    </div>



                    {{-- ROYAL LEGACY --}}
                    @php $tier10 = $tiers->where('level', 10)->first(); @endphp
                    <div
                        class="relative pl-[70px] tier-card bg-[rgba(34,37,59,0.5)] border-y-2 pb-[12px] border-[rgba(217,217,217,0.42)] w-full lg:w-[calc(50%-92px)] lg:pl-[210px] pr-[56px] pt-[24px] lg:ml-auto">
                        <h3 class="tiers_name_10 font-extrabold text-[40px] text-[#caca33]">{{ strtoupper($tier10->name) }}</h3>
                        <div class="h-[39px] w-[265px] border border-[#ff00d8] flex items-center justify-start pl-[4px]">
                            <p class="range_usd_10 font-extrabold text-[24px] text-[#ff00d8]">
                                @if($tier10->max_balance)
                                    ${{ number_format($tier10->min_balance, 0) }} –
                                    ${{ number_format($tier10->max_balance, 0) }} USD
                                @else
                                    ${{ number_format($tier10->min_balance, 0) }}+ USD
                                @endif
                            </p>
                        </div>
                        <div class="space-y-[8px] text-[16px] text-neutral-100 pt-[24px]">
                            @foreach($tier10->percentages as $index => $percentage)
                                <div class="flex justify-between"
                                     style="max-width: 324px;"><span class="days_{{ $index + 1 }} font-semibold"
                                                                     style="width: 109px;">{{ $percentage->days }} DAYS</span><span class="percent_{{ $index + 1 }}"
                                                                                                                                    style="width: 52px;">{{ rtrim(rtrim(number_format($percentage->percentage, 1), '0'), '.') }}%</span>
                                </div>
                            @endforeach
                        </div>
                        {{-- Central tiers image --}}
                        <div
                            class="absolute left-[70%] top-0 md:left-auto md:right-0 md:top-1/2 transform -translate-y-1/2 lg:translate-y-0 w-[103px] h-[103px] md:w-[203px] md:h-[203px] lg:left-[-199px] lg:top-[-48px] px-4 py-4 lg:w-[402px] lg:h-[402px] pointer-events-none z-20">
                            <img src="img/profit_tiers/t10.svg"
                                 alt=""
                                 class="w-[100%] h-[100%] object-cover">
                            <div
                                class="absolute right-[31px] top-[34px] md:right-[60px] md:top-[110px] md:w-[87px] md:h-[45px] text-[22px] md:text-[67px] lg:right-[142px] lg:top-[190px] lg:w-[117px] lg:h-[95px] font-extrabold lg:text-[100px] text-white tracking-[5px] z-30 flex items-end justify-center">
                                10
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Islamic Account Layout --}}
                    @php
                        $tierColors = [
                            1 => '#f6bd0e',
                            2 => '#ff613e',
                            3 => '#f70808',
                            4 => '#e10495',
                            5 => '#c000ca',
                            6 => '#2f7dea',
                            7 => '#1393af',
                            8 => '#29bdb4',
                            9 => '#82ca8a',
                            10 => '#caca33',
                        ];
                        $tierPositions = [
                            1 => 'lg:mb-[180px]',
                            2 => 'lg:mt-[180px] lg:ml-auto lg:pl-[210px]',
                            3 => 'lg:mb-[180px] lg:mt-[-180px]',
                            4 => 'lg:ml-auto lg:pl-[210px]',
                            5 => 'lg:mb-[180px] lg:mt-[-180px]',
                            6 => 'lg:ml-auto lg:pl-[210px]',
                            7 => 'lg:mb-[180px] lg:mt-[-180px]',
                            8 => 'lg:ml-auto lg:pl-[210px]',
                            9 => 'lg:mb-[180px] lg:mt-[-180px]',
                            10 => 'lg:ml-auto lg:pl-[210px]',
                        ];
                        $imagePositions = [
                            1 => 'lg:right-[-206px] lg:top-[-52px]',
                            2 => 'lg:left-[-203px] lg:top-[-47.5px]',
                            3 => 'lg:right-[-202px] lg:top-[-52px]',
                            4 => 'lg:left-[-199.5px] lg:top-[-47.5px]',
                            5 => 'lg:right-[-202px] lg:top-[-52px]',
                            6 => 'lg:left-[-199px] lg:top-[-48px]',
                            7 => 'lg:right-[-202px] lg:top-[-52px]',
                            8 => 'lg:left-[-199px] lg:top-[-48px]',
                            9 => 'lg:right-[-202px] lg:top-[-52px]',
                            10 => 'lg:left-[-199px] lg:top-[-48px]',
                        ];
                    @endphp
                    @foreach($tiers as $tier)
                        @php
                            $percentages = $tier->islamicPercentages()->orderBy('duration_days')->get();
                            $color = $tierColors[$tier->level] ?? '#FFFFFF';
                            $position = $tierPositions[$tier->level] ?? '';
                            $imagePosition = $imagePositions[$tier->level] ?? 'lg:right-[-206px] lg:bottom-[-52px]';
                            $imageSide = in_array($tier->level, [2, 4, 6, 8, 10]) ? 'left-[70%]' : 'left-0';
                            $imageRotation = $tier->level === 1 ? 'rotate-[1deg]' : '';
                        @endphp
                        <div
                            class="relative tier-card pl-[70px] {{ $position }} bg-[rgba(34,37,59,0.5)] border-y-2 pb-[12px] border-[rgba(217,217,217,0.42)] h-full w-full lg:w-[calc(50%-92px)] pl-[56px] pr-[56px] pt-[24px]">
                            <h3 class="font-extrabold text-[40px]"
                                style="color: {{ $color }}">{{ strtoupper($tier->name) }}</h3>
                            <div class="h-[39px] w-auto max-w-[350px] border border-[#ff00d8] flex items-center justify-start pl-[4px]">
                                <p class="font-extrabold text-[24px] text-[#ff00d8]">
                                    @if($tier->max_balance)
                                        ${{ number_format($tier->min_balance, 0) }} –
                                        ${{ number_format($tier->max_balance, 0) }} USDT
                                    @else
                                        ${{ number_format($tier->min_balance, 0) }}+ USDT
                                    @endif
                                </p>
                            </div>
                            <div class="space-y-[8px] text-[16px] text-neutral-100 pt-[24px]">
                                @foreach($percentages as $index => $percentage)
                                    <div class="flex justify-between"
                                         style="max-width: 324px;">
                                        <span class="font-semibold"
                                              style="width: 109px;">{{ $percentage->duration_days }} DAYS</span>
                                        <span style="width: 120px;">{{ rtrim(rtrim(number_format($percentage->min_percentage, 1), '0'), '.') }}% - {{ rtrim(rtrim(number_format($percentage->max_percentage, 1), '0'), '.') }}%</span>
                                    </div>
                                @endforeach
                            </div>
                            {{-- Central tiers image --}}
                            <div
                                class="absolute {{ $imageSide }} top-0 md:left-auto md:right-0 md:top-1/2 transform -translate-y-1/2 lg:translate-y-0 w-[103px] h-[103px] md:w-[203px] md:h-[203px] {{ $imagePosition }} px-4 py-4 lg:w-[402px] lg:h-[402px] pointer-events-none z-20">
                                <img src="{{ asset('img/profit_tiers/t' . $tier->level . '.svg') }}"
                                     alt=""
                                     class="w-[100%] h-[100%] object-cover {{ $imageRotation }}">
                                <div
                                    class="absolute right-[31px] top-[34px] md:right-[60px] md:top-[110px] md:w-[87px] md:h-[45px] text-[22px] md:text-[67px] lg:right-[142px] lg:top-[190px] lg:w-[117px] lg:h-[95px] font-extrabold lg:text-[100px] text-white tracking-[5px] z-30 flex items-end justify-center">
                                    {{ str_pad($tier->level, 2, '0', STR_PAD_LEFT) }}</div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

{{--            Bootom rise--}}

        </div>
        <div class="md:flex flex-col items-center justify-center py-[40px] px-[20px] mb-[25rem]">
            <div class="relative md:flex justify-center w-[1280px] max-w-full">
                {{-- Left Card: Rise Through Every Stake --}}
                <div
                    class="main-left-card absolute md:pr-[20px] left-[-70px] top-[78px] w-[591px] h-[328px] z-[10]">
                    <div
                        class="bg-[rgba(34,37,59,0.5)] px-[20px] py-[48px] rounded-[31px] border border-[rgba(217,217,217,0.42)] w-full h-full relative">
                        <div class="flex flex-col items-center justify-center h-full ">
                            <h1 class="font-bold text-center"
                                style="margin-bottom: 32px; line-height: 0;">
                                <div class="text-[30px] lg:text-[52px] leading-[0.94] text-[#ff00d8] mb-[8px]">Rise
                                    Through
                                </div>
                                <div class="text-[30px] lg:text-[70px] leading-[0.94] text-[#ff00d8]">
                                    Every Stake
                                </div>
                            </h1>
                            <p
                                class="card-description text-white text-center text-[28px] leading-normal max-w-[460px] m-0">
                                Your Journey to Smarter Crypto Earnings Starts Here with Arbitex
                            </p>
                        </div>
                    </div>
                    {{-- Pink accent line --}}
                    <div
                        class="main-accent-line-pink absolute bg-[#ff00d8] left-[25%] md:left-[22%] lg:left-[25%] md:bottom-[-17px]  w-[50%] lg:w-[298px] h-[17px] lg:h-[17px] z-[15]">
                    </div>
                </div>

                {{-- Right Card: Dynamic Earning Tiers --}}
                <div class="main-right-card absolute left-[756px] top-[216px] w-[591px] h-[328px] z-[10] ">
                    {{-- Orange accent line --}}
                    <div
                        class="hidden md:block main-accent-line-orange absolute bg-[#ff451c] left-[25%] md:left-[22%] lg:left-[25%]  md:bottom-[-17px]  w-[50%] lg:w-[298px] h-[17px] lg:h-[17px] z-[15]">
                    </div>
                    <div
                        class="bg-[rgba(34,37,59,0.5)] px-[20px] py-[48px] rounded-[31px] border border-[rgba(217,217,217,0.42)] w-full h-full relative">
                        <div class="flex flex-col items-center justify-center h-full">
                            <h1 class="font-bold text-center"
                                style="margin-bottom: 32px; line-height: 0;">
                                <div
                                <div class="text-[30px] lg:text-[52px] leading-[0.94] text-[#ff451c] mb-[8px]">
                                    Dynamic
                                </div>
                                <div class="text-[30px] lg:text-[70px] leading-[0.94] text-[#ff451c]">
                                    Earning Tiers
                                </div>
                            </h1>
                            <p
                                class="card-description text-white text-center text-[28px] leading-normal max-w-[460px] m-0">
                                Maximize Returns at Every Level of Your Lumastake Staking Experience
                            </p>
                        </div>
                    </div>
                    {{-- Orange accent line for mobile --}}
                    <div
                        class="md:hidden main-accent-line-orange absolute bg-[#ff451c] left-[25%] md:left-[22%] lg:left-[25%]  md:bottom-[-17px]  w-[50%] lg:w-[298px] h-[17px] lg:h-[17px] z-[15]">
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
