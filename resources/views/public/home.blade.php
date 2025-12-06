@extends('layouts.public')

@section('content')
    {{-- Hi-Tech BG with animated grid and particles --}}
    <div class="bg-two-sections">
        <div class="bg-two-sections__overlay"></div>

        {{-- Animated tech grid --}}
        <div class="tech-grid"></div>

        {{-- Scanning line effect --}}
        <div class="scan-line"></div>

        {{-- Hex pattern overlay --}}
        <div class="hex-pattern"></div>

        <div class="relative z-10">

            {{-- HERO SECTION --}}
            <section class="hero section-relative flex items-center">
                {{-- Tech corners --}}
                <div class="tech-corner top-left"></div>
                <div class="tech-corner top-right"></div>

                {{-- Gradient orbs with pulse --}}
                <div class="absolute inset-0">
                    <div class="blob blob--lg blob--green glow-pulse" style="top:-120px; left:-120px"></div>
                    <div class="blob blob--md blob--pink glow-pulse" style="top:140px; right:-80px; animation-delay: 1s"></div>
                    <div class="blob blob--sm blob--orange glow-pulse" style="bottom:-40px; left:20%; animation-delay: 2s"></div>
                </div>

                {{-- Floating particles --}}
                <div class="particle" style="top: 20%; left: 15%;"></div>
                <div class="particle" style="top: 60%; left: 80%;"></div>
                <div class="particle" style="top: 40%; left: 50%;"></div>
                <div class="particle" style="top: 75%; left: 25%;"></div>
                <div class="particle" style="top: 30%; left: 70%;"></div>

                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <div class="grid lg:grid-cols-2 gap-12 items-center">
                        <div class="space-y-8">
                            <div class="space-y-4 animate-slide-in-left">
                                <h1 class="text-4xl md:text-6xl font-extrabold tech-accent">
                                    <span class="text-white">CRYPTO</span>
                                    <span class="text-white font-normal"> MADE it EASY</span>
                                </h1>
                                <h2 class="text-6xl md:text-8xl lg:text-9xl font-extrabold gradient-text tech-flicker">STAKING</h2>
                                <h3 class="text-3xl md:text-5xl font-normal text-white">MADE SMARTER</h3>
                            </div>

                            <p class="text-lg md:text-xl text-white/80 max-w-2xl animate-slide-in-left delay-200">
                                Welcome to <span class="font-bold text-arbitex-orange">Lumastake</span> a platform where your
                                <span class="font-bold text-arbitex-pink">USDT</span> grows securely, without trading or market
                                noise. All you need is a wallet, a plan, and a few minutes.
                            </p>

                            <div class="flex flex-col sm:flex-row gap-3 animate-slide-in-left delay-300">
                                <button class="btn btn--orange active-border"><a href="{{url('/register')}}">Start Staking</a></button>
                                <button class="btn btn--green active-border"><a href="{{url('/tiers')}}">Explore Plans</a></button>
                            </div>
                        </div>

                        <div class="relative">
                            {{-- Data stream lines --}}
                            <div class="data-line" style="top: 20%; animation-delay: 0s;"></div>
                            <div class="data-line" style="top: 40%; animation-delay: 1s;"></div>
                            <div class="data-line" style="top: 60%; animation-delay: 2s;"></div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- WHY CHOOSE SECTION --}}
            <section id="about" class="py-20 relative">
                {{-- Floating particles --}}
                <div class="particle" style="top: 10%; left: 85%;"></div>
                <div class="particle" style="top: 50%; left: 10%;"></div>

                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <div class="why-panel pb-8 md:pb-12 lg:pb-16 relative overflow-hidden holographic">
                        <span class="why-cap" aria-hidden="true"></span>

                        <div class="relative z-10">
                            <div class="why-head mb-10 text-left animate-slide-up">
                                <h2 class="text-4xl sm:text-5xl md:text-6xl font-audiowide text-white mb-3">
                                    WHY CHOOSE
                                </h2>
                                <h2 class="text-5xl sm:text-8xl md:text-9xl font-audiowide text-white tech-flicker">
                                    LUMASTAKE
                                </h2>
                            </div>

                            <div class="grid md:grid-cols-2 gap-8 text-left">
                                <div class="space-y-6 animate-slide-in-left delay-300">
                                    <h4 class="text-2xl font-medium text-arbitex-pink tech-accent">
                                        Because crypto should work for you — not confuse you.
                                    </h4>
                                    <p class="text-lg text-white/80">
                                        Lumastake is designed for clarity, safety, and real returns. No complex charts.
                                        No price speculation. Just stablecoin staking that delivers.
                                    </p>
                                </div>

                                <div class="space-y-4 animate-slide-in-right delay-400">
                                    <ul class="space-y-3 text-white/80">
                                        <li class="flex items-start">
                                            <span class="w-2 h-2 bg-arbitex-green rounded-full mt-2 mr-3 flex-shrink-0 glow-pulse"></span>
                                            100% staking-based income
                                        </li>
                                        <li class="flex items-start">
                                            <span class="w-2 h-2 bg-arbitex-green rounded-full mt-2 mr-3 flex-shrink-0 glow-pulse"></span>
                                            Zero exposure to trading or leverage
                                        </li>
                                        <li class="flex items-start">
                                            <span class="w-2 h-2 bg-arbitex-green rounded-full mt-2 mr-3 flex-shrink-0 glow-pulse"></span>
                                            Transparent returns — always visible
                                        </li>
                                        <li class="flex items-start">
                                            <span class="w-2 h-2 bg-arbitex-green rounded-full mt-2 mr-3 flex-shrink-0 glow-pulse"></span>
                                            Built for everyday users, not just tech experts
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div> {{-- End z-10 wrapper --}}
    </div> {{-- End bg-two-sections --}}

    {{-- FEATURES SECTION --}}
    <section id="profit-tiers" class="py-20 relative">
        {{-- Tech grid background --}}
        <div class="tech-grid opacity-50"></div>

        {{-- Floating particles --}}
        <div class="particle" style="top: 15%; left: 90%;"></div>
        <div class="particle" style="top: 65%; left: 5%;"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16 animate-slide-up">
                <h2 class="text-5xl md:text-7xl font-extrabold text-white mb-6 tech-flicker">FEATURES</h2>
                <p class="text-xl md:text-2xl text-white/80 max-w-4xl mx-auto">
                    Everything you need to grow your crypto, nothing you don't.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                {{-- Feature 1 --}}
                <div class="feature-card glass-effect rounded-lg p-8 text-center relative group hover:scale-105 transition-transform glow-orange holographic animate-expand delay-100">
                    <div class="tech-corner top-left"></div>
                    <div class="tech-corner bottom-right"></div>
                    <div class="feature-icon">
                        <img src="img/staking.svg" alt="Staking" class="size-[5.5rem] relative z-10">
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-4">
                        Staking Periods from<br><span class="font-bold">10 to 180 Days</span>
                    </h3>
                </div>

                {{-- Feature 2 --}}
                <div class="feature-card glass-effect rounded-lg p-8 text-center relative group hover:scale-105 transition-transform glow-pink holographic animate-expand delay-200">
                    <div class="tech-corner top-right"></div>
                    <div class="tech-corner bottom-left"></div>
                    <div class="feature-icon">
                        <img src="img/dashboard.svg" alt="Dashboard" class="size-[5.5rem] relative z-10">
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-4">
                        Live Dashboard to<br><span class="font-bold">Track Your Growth</span>
                    </h3>
                </div>

                {{-- Feature 3 --}}
                <div class="feature-card glass-effect rounded-lg p-8 text-center relative group hover:scale-105 transition-transform glow-green holographic animate-expand delay-300">
                    <div class="tech-corner top-left"></div>
                    <div class="tech-corner bottom-right"></div>
                    <div class="feature-icon">
                        <img src="img/usdt-withdraw.svg" alt="Withdrawals" class="size-[5.5rem] relative z-10">
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-4">
                        Quick USDT Deposits &<br><span class="font-bold">Withdrawals</span>
                    </h3>
                </div>
            </div>

            {{-- Features 4-5 --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-16 lg:w-fit mx-auto place-items-center">
                <div class="feature-card glass-effect rounded-lg p-8 text-center relative group hover:scale-105 transition-transform glow-indigo w-full sm:w-[320px] md:w-[360px] lg:w-[420px] holographic animate-expand delay-400">
                    <div class="tech-corner top-right"></div>
                    <div class="tech-corner bottom-left"></div>
                    <div class="feature-icon">
                        <img src="img/earning-model.svg" alt="Multi-tier" class="size-[5.5rem] relative z-10">
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-4">
                        Multi-Tier<br><span class="font-bold">Earning Model</span>
                    </h3>
                </div>

                <div class="feature-card glass-effect rounded-lg p-8 text-center relative group hover:scale-105 transition-transform glow-cyan w-full sm:w-[320px] md:w-[360px] lg:w-[420px] holographic animate-expand delay-500">
                    <div class="tech-corner top-left"></div>
                    <div class="tech-corner bottom-right"></div>
                    <div class="feature-icon">
                        <img src="img/devices.svg" alt="Mobile" class="size-[5.5rem] relative z-10">
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-4">
                        Works Seamlessly on<br><span class="font-bold">Mobile & Desktop</span>
                    </h3>
                </div>
            </div>
        </div>
    </section>

    {{-- HOW IT WORKS SECTION --}}
    <section class="py-20 relative">
        {{-- Tech grid --}}
        <div class="tech-grid opacity-30"></div>

        {{-- Particles --}}
        <div class="particle" style="top: 25%; left: 10%;"></div>
        <div class="particle" style="top: 70%; left: 85%;"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="pb-32 text-center mb-16">
                <div class="relative">
                    {{-- Glow effect background --}}
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <img src="/images/figma/shadow-alipse.png" alt="" class="w-[600px] h-[600px] md:w-[800px] md:h-[800px] object-contain opacity-60 glow-pulse">
                    </div>
                    <h2 class="text-[10rem] sm:text-[8rem] md:text-[14rem] lg:text-[15rem] font-audiowide text-white/10 absolute inset-0 flex items-center justify-center pointer-events-none select-none">
                        WORKS
                    </h2>
                    <div class="relative z-10 animate-slide-up">
                        <h3 class="text-5xl md:text-7xl font-audiowide text-white mb-6 tech-flicker">
                            HOW IT WORKS
                        </h3>
                        <p class="text-xl md:text-2xl text-white/80">
                            Getting started takes just minutes.
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                {{-- Step 1 --}}
                <div class="relative animate-slide-up delay-200">
                    <div class="steps bg-arbitex-orange rounded-3xl p-8 text-center h-96 flex flex-col justify-center holographic active-border">
                        <div class="tech-corner top-left"></div>
                        <div class="tech-corner bottom-right"></div>
                        <div class="first-step step-number text-8xl md:text-9xl font-audiowide text-white">1</div>
                        <h3 class="text-3xl md:text-4xl font-extrabold text-white mb-6 relative z-10">
                            SIGN UP
                        </h3>
                        <p class="text-lg text-white/90 relative z-10">
                            Create your account it's fast, simple, and free.
                        </p>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="relative animate-slide-up delay-400">
                    <div class="steps bg-arbitex-pink rounded-3xl p-8 text-center h-96 flex flex-col justify-center holographic active-border">
                        <div class="tech-corner top-right"></div>
                        <div class="tech-corner bottom-left"></div>
                        <div class="step-number text-8xl md:text-9xl font-audiowide text-white">2</div>
                        <h3 class="text-3xl md:text-4xl font-extrabold text-white mb-6 relative z-10">
                            Deposit USDT
                        </h3>
                        <p class="text-lg text-white/90 relative z-10">
                            Add funds quickly and securely.
                        </p>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div class="relative animate-slide-up delay-600">
                    <div class="steps bg-arbitex-green rounded-3xl p-8 text-center h-96 flex flex-col justify-center holographic active-border">
                        <div class="tech-corner top-left"></div>
                        <div class="tech-corner bottom-right"></div>
                        <div class="step-number text-8xl md:text-9xl font-audiowide text-white">3</div>
                        <h3 class="text-3xl md:text-4xl font-extrabold text-white mb-6 relative z-10">
                            Choose a Staking Plan
                        </h3>
                        <p class="text-lg text-white/90 relative z-10">
                            Pick your duration. Your USDT starts working immediately.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- OPPORTUNITY SECTION --}}
    <section class="py-20 relative">
        {{-- Hex pattern --}}
        <div class="hex-pattern"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-8 animate-slide-in-left">
                    <div class="space-y-4 tech-accent">
                        <h2 class="text-2xl md:text-3xl font-bold text-white">
                            Turning Crypto Complexity into an
                        </h2>
                        <h3 class="text-6xl md:text-7xl font-bold gradient-text tech-flicker">
                            Opportunity
                        </h3>
                    </div>

                    <div class="bg-white p-6 rounded-lg active-border">
                        <p class="text-2xl md:text-3xl font-black text-arbitex-dark">
                            Less confusion. More control.
                        </p>
                    </div>

                    <p class="text-lg md:text-xl text-white/80">
                        We built Lumastake to remove the noise from crypto. No tokens to flip. No coins to trade. Just one
                        goal: make your stablecoins earn more, safely and smoothly.
                    </p>

                    <button class="px-8 py-4 bg-arbitex-green text-arbitex-dark font-semibold rounded-lg hover:bg-arbitex-green/90 transition-colors active-border">
                        <a href="{{url('/register')}}">Start Staking</a>
                    </button>
                </div>

                <div class="relative animate-slide-in-right">
                    <div class="tech-corner top-left"></div>
                    <div class="tech-corner bottom-right"></div>
                    <img src="assets/f3c661a3bc2a4f8ec145354d872fc4b44e1c6209.png"
                         alt="Opportunity"
                         class="w-full h-auto">
                </div>
            </div>
        </div>
    </section>

    {{-- BENEFITS SECTION --}}
    <section class="py-20 gradient-bg relative">
        {{-- Scan line --}}
        <div class="scan-line"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16 animate-slide-up">
                <h2 class="text-5xl sm:text-8xl md:text-9xl font-audiowide text-white mb-6 tech-flicker">
                    BENEFITS
                </h2>
                <p class="text-xl md:text-2xl text-white/90">
                    Why people are switching to Arbitex
                </p>
            </div>
        </div>
    </section>

    <div class="container-fixed hidden lg:block animate-fade-in-up relative">
        {{-- Particles --}}
        <div class="particle" style="top: 30%; left: 5%;"></div>
        <div class="particle" style="top: 60%; left: 95%;"></div>

        <div class="benefits-flow">
            <div class="flow-step c-lime holographic">
                <img class="icon" src="img/benifists/1.svg" alt="">
                <div class="title">Fixed returns,<br>no price swings</div>
            </div>

            <div class="flow-step c-green holographic">
                <div class="title">Hands-off experience<br>no trading needed</div>
                <img class="icon" src="img/benifists/2.svg" alt="">
            </div>

            <div class="flow-step c-violet holographic">
                <img class="icon" src="img/benifists/3.svg" alt="">
                <div class="title">Tier-based rewards<br>system</div>
            </div>

            <div class="flow-step c-purple holographic">
                <div class="title">Mobile<br>friendly interface</div>
                <img class="icon" src="img/benifists/4.svg" alt="">
            </div>

            <div class="flow-step c-orange holographic">
                <img class="icon" src="img/benifists/5.svg" alt="">
                <div class="title">Clean, clutter-free<br>experience</div>
            </div>

            <div class="flow-step c-mint holographic">
                <div class="title">Transparent<br>from day one</div>
                <img class="icon" src="img/benifists/6.svg" alt="">
            </div>
        </div>
    </div>

    {{-- SECURITY SECTION --}}
    <section id="faq" class="py-20 relative">
        {{-- Tech grid --}}
        <div class="tech-grid opacity-40"></div>

        {{-- Particles --}}
        <div class="particle" style="top: 20%; left: 90%;"></div>
        <div class="particle" style="top: 75%; left: 10%;"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16">
                <div class="relative">
                    {{-- Glow effect background --}}
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <img src="/images/figma/shadow-alipse.png" alt="" class="w-[600px] h-[600px] md:w-[800px] md:h-[800px] object-contain opacity-60 glow-pulse">
                    </div>
                    <h2 class="text-[10rem] sm:text-[18rem] md:text-[14rem] lg:text-[15rem] font-audiowide text-white/10 absolute inset-0 flex items-center justify-center pointer-events-none select-none">
                        SECURE
                    </h2>
                    <div class="relative z-10 animate-slide-up">
                        <h3 class="text-6xl lg:text-7xl font-audiowide text-white mb-6 tech-flicker">
                            SAFE & SECURE
                        </h3>
                        <p class="text-xl md:text-2xl text-white/80">
                            Your money stays protected. Always.
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                {{-- Security features --}}
                <div class="glass-effect rounded-2xl p-10 security-card holographic animate-slide-up delay-100">
                    <h3 class="text-lg font-semibold text-white">
                        Strong encryption protects your wallet
                    </h3>
                </div>

                <div class="glass-effect rounded-2xl p-10 security-card holographic animate-slide-up delay-200">
                    <h3 class="text-lg font-semibold text-white">
                        Two-factor authentication (2FA)
                    </h3>
                </div>

                <div class="glass-effect rounded-2xl p-10 security-card holographic animate-slide-up delay-300">
                    <h3 class="text-lg font-semibold text-white">
                        All deposits and withdrawals in USDT
                    </h3>
                </div>

                <div class="glass-effect rounded-2xl p-10 security-card holographic animate-slide-up delay-400">
                    <h3 class="text-lg font-semibold text-white">
                        Cold wallet storage for most funds
                    </h3>
                </div>

                <div class="glass-effect rounded-2xl p-10 security-card holographic animate-slide-up delay-500">
                    <h3 class="text-lg font-semibold text-white">
                        Regular system checks and audits
                    </h3>
                </div>

                <div class="glass-effect rounded-2xl p-10 security-card holographic animate-slide-up delay-600">
                    <h3 class="text-lg font-semibold text-white">
                        Instant withdrawals, just minutes away
                    </h3>
                </div>
            </div>
        </div>
    </section>

    {{-- NEWS & INSIGHTS SECTION --}}
    <section id="blog" class="py-44 relative"
             style="background-image: url('assets/news-ins-bg.png'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        {{-- Mobile background overlay for better readability --}}
        <div class="absolute inset-0 bg-black/30 md:bg-black/20"></div>

        {{-- Hex pattern --}}
        <div class="hex-pattern opacity-50"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="insights-news glass-effect-light p-12 relative py-20 holographic animate-slide-up">
                <div class="absolute left-0 top-0 bottom-0 w-2 rounded-l-2xl"></div>
                <div class="pl-8">
                    <h2 class="text-5xl md:text-6xl font-extrabold text-white mb-6 tech-accent">
                        News & Insights
                    </h2>
                    <p class="text-xl md:text-2xl text-white/80 mb-8">
                        Stay ahead with the latest in staking and stablecoins.
                    </p>

                    <div class="space-y-4 mt-20">
                        <h3 class="text-4xl md:text-5xl font-audiowide text-arbitex-green tech-flicker">
                            COMING SOON
                        </h3>
                        <p class="text-lg md:text-xl text-white/80 max-w-3xl">
                            Lumastake Academy — tips, guides, and expert interviews to help you make smarter crypto moves.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA SECTION --}}
    <section id="contact" class="py-44 relative">
        {{-- Tech grid --}}
        <div class="tech-grid opacity-30"></div>

        {{-- Particles --}}
        <div class="particle" style="top: 40%; left: 15%;"></div>
        <div class="particle" style="top: 60%; left: 80%;"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-left relative z-10">
            <div class="space-y-8 animate-slide-in-left">
                <h2 class="text-5xl sm:text-8xl md:text-9xl font-audiowide inline-block bg-[linear-gradient(90deg,_#00FFA3_0%,_#FF00D8_57%,_#FF451C_100%)] bg-clip-text text-transparent [-webkit-text-fill-color:transparent] tech-flicker">
                    WELCOME!
                </h2>
                <h3 class="text-3xl md:text-4xl font-extrabold text-white tech-accent">
                    It's Time to Take the Leap — It's Arbitex
                </h3>
            </div>
        </div>
    </section>

    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in-up');
                }
            });
        }, observerOptions);

        // Observe elements for animation
        document.querySelectorAll('section').forEach(section => {
            observer.observe(section);
        });

        // Generate random particles
        function createRandomParticles(count = 10) {
            const body = document.querySelector('.bg-two-sections');
            if (!body) return;

            for (let i = 0; i < count; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.top = Math.random() * 100 + '%';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 6 + 's';
                particle.style.animationDuration = (4 + Math.random() * 4) + 's';

                // Random color
                const colors = ['#00ffa3', '#ff00d8', '#ff451c'];
                const color = colors[Math.floor(Math.random() * colors.length)];
                particle.style.background = color;
                particle.style.boxShadow = `0 0 10px ${color}`;

                body.appendChild(particle);
            }
        }

        // Create particles on load
        document.addEventListener('DOMContentLoaded', () => {
            createRandomParticles(15);
        });
    </script>

@endsection
