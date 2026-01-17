@extends('layouts.public')

@section('content')
<div class="min-h-screen relative flex items-center justify-center py-10 md:py-20 overflow-hidden bg-white" x-data="loginForm()" x-cloak>
    {{-- Background Image --}}
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('img/login/login-bg.png') }}" alt="Background" class="w-full h-full object-cover">
    </div>

    {{-- Decor Ellipses --}}
    <div class="absolute top-[514px] left-[950px] w-[552px] h-[552px] bg-[#3B4EFC]/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-[-264px] left-[-530px] w-[1189px] h-[1189px] bg-[#3B4EFC]/5 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 relative z-10 w-full flex flex-col items-center">
        {{-- Header Titles (Login / Welcome Back) --}}
        <div class="text-center mb-8 md:mb-12">
            <h1 class="text-4xl md:text-6xl font-black text-[#3B4EFC] mb-2 md:mb-4 uppercase tracking-tight"
                x-text="step.includes('forgot') ? 'Forgot Password' : (step === 'login-stay' ? 'Security Verification' : 'Login')">
            </h1>
            <p class="text-xl md:text-2xl text-[#262262] font-medium"
               x-text="step === 'login-email' ? 'Welcome to Luma Stake Dashboard' : (step === 'login-stay' ? 'Stay Logged in' : 'Welcome Back!')">
            </p>
        </div>

        {{-- Main Card --}}
        <div class="bg-white border border-[#2BA6FF] rounded-[20px] shadow-[0_4px_30px_rgba(43,166,255,0.25)] w-full max-w-[900px] relative flex flex-col items-center p-6 md:p-12">

            {{-- Back Button --}}
            <button x-show="step !== 'login-email' && step !== 'login-stay'"
                    @click="goBack()"
                    class="absolute left-6 md:left-12 top-6 md:top-8 flex items-center gap-2 md:gap-4 text-[#CCCCCC] hover:text-[#3B4EFC] transition-colors group">
                <div class="w-8 h-8 md:w-10 md:h-10 border-2 border-[#CCCCCC] group-hover:border-[#3B4EFC] rounded-full flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 md:w-6 md:h-6 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
                <span class="text-xl md:text-2xl font-medium">Back</span>
            </button>

            {{-- Logo --}}
            <div class="mb-8 md:mb-10">
                <img src="{{ asset('img/home/logo-final.png') }}" alt="LumaStake" class="h-12 md:h-16">
            </div>

            <div class="w-full max-w-[600px] flex-grow flex flex-col justify-center">

                {{-- STEP 1: LOGIN EMAIL --}}
                <div x-show="step === 'login-email'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0">
                    <div class="space-y-6 md:space-y-8">
                        <div class="relative">
                            <input type="text" x-model="formData.email" placeholder="Email/ Phone Number"
                                   @keydown.enter="nextToPassword()"
                                   class="w-full bg-[#E0F2FF] border border-[#2BA6FF] rounded-xl px-6 md:px-8 py-4 md:py-6 text-xl md:text-2xl text-[#262262] focus:outline-none focus:ring-4 focus:ring-[#3B4EFC]/10 transition-all placeholder:text-[rgba(0,0,0,0.4)]">
                            <template x-if="errors.email">
                                <p class="text-red-500 mt-2 md:mt-4 text-lg md:text-xl font-bold" x-text="errors.email"></p>
                            </template>
                        </div>

                        <button @click="nextToPassword()" :disabled="loading"
                                class="w-full bg-[#3B4EFC] text-white py-4 md:py-6 rounded-xl text-2xl md:text-3xl font-black hover:bg-[#262262] transition-all transform active:scale-[0.98] shadow-xl flex items-center justify-center gap-4">
                            <span x-show="!loading">Next</span>
                            <span x-show="loading" class="animate-spin border-4 border-white/30 border-t-white rounded-full w-8 h-8 md:w-10 md:h-10"></span>
                        </button>

                        {{-- Divider --}}
                        <div class="flex items-center gap-6 md:gap-10 py-2 md:py-4">
                            <div class="flex-1 h-[2px] md:h-[3px] bg-[#D5D5D5]"></div>
                            <span class="text-xl md:text-2xl text-[rgba(0,0,0,0.5)]">or</span>
                            <div class="flex-1 h-[2px] md:h-[3px] bg-[#D5D5D5]"></div>
                        </div>

                        {{-- Google Sign In --}}
                        <a href="{{ route('social.login', 'google') }}" class="w-full flex items-center justify-center gap-4 md:gap-6 py-4 md:py-6 bg-white border border-[#D5D5D5] rounded-xl text-xl md:text-2xl font-medium text-black hover:bg-gray-50 transition-all shadow-sm">
                            <img src="{{ asset('img/login/e0065f7edb9ecd75cff6c9f4b3fb15948ce63d12.svg') }}" alt="Google" class="w-8 h-8 md:w-10 md:h-10">
                            Sign in with Google
                        </a>

                        <p class="text-xl md:text-2xl text-[#CCCCCC] text-center mt-6 md:mt-8">
                            Do have an account? <a href="{{ route('register') }}" class="text-[#3B4EFC] font-black hover:underline">Signup</a>
                        </p>
                    </div>
                </div>

                {{-- STEP 2: LOGIN PASSWORD --}}
                <div x-show="step === 'login-password'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0">
                    <div class="space-y-6 md:space-y-8">
                        <div class="relative">
                            <input type="password" x-model="formData.password" placeholder="Enter Password"
                                   @keydown.enter="attemptLogin()"
                                   class="w-full bg-[#E0F2FF] border border-[#2BA6FF] rounded-xl px-6 md:px-8 py-4 md:py-6 text-xl md:text-2xl text-[#262262] focus:outline-none focus:ring-4 focus:ring-[#3B4EFC]/10 transition-all placeholder:text-[rgba(0,0,0,0.4)]">

                            {{-- Eye Icon Toggle --}}
                            <button type="button" @click="togglePassword($el)" class="absolute right-6 md:right-8 top-1/2 -translate-y-1/2 text-[#2BA6FF]">
                                <img src="{{ asset('img/login/f5db2e8213dae77711adde1ebe72e3ce4259bf89.png') }}" alt="Show" class="w-8 h-6 md:w-10 md:h-8 opacity-70">
                            </button>

                            <template x-if="errors.password">
                                <p class="text-red-500 mt-2 md:mt-4 text-lg md:text-xl font-bold" x-text="errors.password"></p>
                            </template>
                        </div>

                        <div class="flex justify-center">
                            <button @click="step = 'forgot-email'" class="text-[#3B4EFC] text-xl md:text-2xl font-medium hover:underline">Forgot Password?</button>
                        </div>

                        <button @click="attemptLogin()" :disabled="loading"
                                class="w-full bg-[#3B4EFC] text-white py-4 md:py-6 rounded-xl text-2xl md:text-3xl font-black hover:bg-[#262262] transition-all transform active:scale-[0.98] shadow-xl flex items-center justify-center gap-4">
                            <span x-show="!loading">Next</span>
                            <span x-show="loading" class="animate-spin border-4 border-white/30 border-t-white rounded-full w-8 h-8 md:w-10 md:h-10"></span>
                        </button>
                    </div>
                </div>

                {{-- STEP 3: 2FA CODE --}}
                <div x-show="step === 'login-2fa'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0">
                    <div class="space-y-6 md:space-y-8">
                        <p class="text-xl md:text-2xl text-[#262262] text-center leading-relaxed mb-4">
                            Enter the 6-digit Verification Code sent to <span class="font-bold text-[#3B4EFC]" x-text="maskEmail(formData.email)"></span>.
                        </p>

                        <div class="relative">
                            <input type="text" x-model="formData.code" maxlength="6" placeholder="Email Verification Code"
                                   @keydown.enter="verify2FA()"
                                   class="w-full bg-[#E0F2FF] border border-[#2BA6FF] rounded-xl px-6 md:px-8 py-4 md:py-6 text-xl md:text-2xl text-[#262262] tracking-[0.2em] text-center focus:outline-none focus:ring-4 focus:ring-[#3B4EFC]/10 transition-all placeholder:text-[rgba(0,0,0,0.4)] placeholder:tracking-normal">

                            <div class="absolute right-6 md:right-8 top-1/2 -translate-y-1/2 pointer-events-none hidden md:block">
                                <span class="text-[#3B4EFC] font-black text-xl uppercase">Code Sent</span>
                            </div>

                            <template x-if="errors.code">
                                <p class="text-red-500 mt-2 md:mt-4 text-lg md:text-xl font-bold text-center" x-text="errors.code"></p>
                            </template>
                        </div>

                        <div class="text-center">
                            <button @click="resend2FA()" :disabled="resendTimer > 0 || loading" class="text-[#3B4EFC] text-lg md:text-xl font-bold hover:underline disabled:text-gray-400 transition-colors">
                                <span x-show="resendTimer === 0">Resend Code</span>
                                <span x-show="resendTimer > 0" x-text="'Resend in ' + resendTimer + 's'"></span>
                            </button>
                        </div>

                        <button @click="verify2FA()" :disabled="loading"
                                class="w-full bg-[#3B4EFC] text-white py-4 md:py-6 rounded-xl text-2xl md:text-3xl font-black hover:bg-[#262262] transition-all transform active:scale-[0.98] shadow-xl flex items-center justify-center gap-4">
                            <span x-show="!loading">Submit</span>
                            <span x-show="loading" class="animate-spin border-4 border-white/30 border-t-white rounded-full w-8 h-8 md:w-10 md:h-10"></span>
                        </button>
                    </div>
                </div>

                {{-- STEP 4: STAY LOGGED IN --}}
                <div x-show="step === 'login-stay'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0">
                    <div class="space-y-6 md:space-y-8">
                        <p class="text-xl md:text-2xl text-[#262262] leading-relaxed text-center font-medium">
                            By clicking “Yes”, You can stay logged in for up to 5 days on this device. To revoke your logged in status, log out of your luma stake account on this device.
                        </p>

                        <div class="flex items-center justify-center gap-4">
                            <input type="checkbox" id="dont-show-stay" x-model="formData.remember" class="w-6 h-6 md:w-8 md:h-8 border-2 border-[#262262] rounded-lg cursor-pointer accent-[#3B4EFC]">
                            <label for="dont-show-stay" class="text-lg md:text-xl text-[#262262] font-semibold cursor-pointer">Don’t show this message again on this device.</label>
                        </div>

                        <div class="space-y-4">
                            <button @click="completeLogin()" class="w-full bg-[#3B4EFC] text-white py-4 md:py-6 rounded-xl text-2xl md:text-3xl font-black hover:bg-[#262262] transition-all shadow-xl">
                                Yes
                            </button>
                            <button @click="completeLogin()" class="w-full border-2 border-[#262262] text-[#262262] py-4 md:py-6 rounded-xl text-2xl md:text-3xl font-black hover:bg-gray-50 transition-all">
                                Not Now
                            </button>
                        </div>
                    </div>
                </div>

                {{-- FORGOT PASSWORD FLOW --}}
                {{-- 5: FORGOT EMAIL --}}
                <div x-show="step === 'forgot-email'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0">
                    <div class="space-y-6 md:space-y-8">
                        <p class="text-xl md:text-2xl text-[#262262] mb-6 md:mb-8 text-center leading-relaxed">No worries! Enter your email and we'll send you an OTP to reset your password.</p>

                        <div class="relative">
                            <input type="email" x-model="formData.email" placeholder="Enter Your Email"
                                   @keydown.enter="sendForgotOTP()"
                                   class="w-full bg-[#E0F2FF] border border-[#2BA6FF] rounded-xl px-6 md:px-8 py-4 md:py-6 text-xl md:text-2xl text-[#262262] focus:outline-none focus:ring-4 focus:ring-[#3B4EFC]/10 transition-all placeholder:text-[rgba(0,0,0,0.4)]">
                            <template x-if="errors.forgot_email">
                                <p class="text-red-500 mt-2 md:mt-4 text-lg md:text-xl font-bold text-center" x-text="errors.forgot_email"></p>
                            </template>
                        </div>

                        <button @click="sendForgotOTP()" :disabled="loading"
                                class="w-full bg-[#3B4EFC] text-white py-4 md:py-6 rounded-xl text-2xl md:text-3xl font-black hover:bg-[#262262] transition-all shadow-xl flex items-center justify-center gap-4">
                            <span x-show="!loading">Send OTP</span>
                            <span x-show="loading" class="animate-spin border-4 border-white/30 border-t-white rounded-full w-8 h-8 md:w-10 md:h-10"></span>
                        </button>
                    </div>
                </div>

                {{-- 6: FORGOT OTP --}}
                <div x-show="step === 'forgot-otp'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0">
                    <div class="space-y-6 md:space-y-8">
                        <p class="text-xl md:text-2xl text-[#262262] mb-6 md:mb-8 text-center">We've sent a 6-digit code to <span class="font-bold text-[#3B4EFC]" x-text="maskEmail(formData.email)"></span></p>

                        <div class="relative">
                            <input type="text" x-model="formData.forgot_code" maxlength="6" placeholder="Enter OTP"
                                   @keydown.enter="verifyForgotOTP()"
                                   class="w-full bg-[#E0F2FF] border border-[#2BA6FF] rounded-xl px-6 md:px-8 py-4 md:py-6 text-xl md:text-2xl text-[#262262] tracking-[0.2em] text-center focus:outline-none focus:ring-4 focus:ring-[#3B4EFC]/10 transition-all placeholder:text-[rgba(0,0,0,0.4)] placeholder:tracking-normal">
                            <template x-if="errors.forgot_code">
                                <p class="text-red-500 mt-2 md:mt-4 text-lg md:text-xl font-bold text-center" x-text="errors.forgot_code"></p>
                            </template>
                        </div>

                        <div class="text-center">
                            <button @click="sendForgotOTP()" :disabled="resendTimer > 0 || loading" class="text-[#3B4EFC] text-lg md:text-xl font-bold hover:underline disabled:text-gray-400">
                                <span x-show="resendTimer === 0">Resend OTP</span>
                                <span x-show="resendTimer > 0" x-text="'Resend in ' + resendTimer + 's'"></span>
                            </button>
                        </div>

                        <button @click="verifyForgotOTP()" :disabled="loading"
                                class="w-full bg-[#3B4EFC] text-white py-4 md:py-6 rounded-xl text-2xl md:text-3xl font-black hover:bg-[#262262] transition-all shadow-xl flex items-center justify-center gap-4">
                            <span x-show="!loading">Verify Code</span>
                            <span x-show="loading" class="animate-spin border-4 border-white/30 border-t-white rounded-full w-8 h-8 md:w-10 md:h-10"></span>
                        </button>
                    </div>
                </div>

                {{-- 7: NEW PASSWORD --}}
                <div x-show="step === 'forgot-password'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0">
                    <div class="space-y-6 md:space-y-8">
                        <p class="text-xl md:text-2xl text-[#262262] mb-6 md:mb-8 text-center leading-relaxed">Enter your new password must be different from previous used password.</p>

                        <div class="space-y-4 md:space-y-6">
                            <input type="password" x-model="formData.new_password" placeholder="New Password"
                                   class="w-full bg-[#E0F2FF] border border-[#2BA6FF] rounded-xl px-6 md:px-8 py-4 md:py-6 text-xl md:text-2xl text-[#262262] focus:outline-none focus:ring-4 focus:ring-[#3B4EFC]/10 transition-all placeholder:text-[rgba(0,0,0,0.4)]">

                            <input type="password" x-model="formData.new_password_confirmation" placeholder="Confirm Password"
                                   @keydown.enter="resetPassword()"
                                   class="w-full bg-[#E0F2FF] border border-[#2BA6FF] rounded-xl px-6 md:px-8 py-4 md:py-6 text-xl md:text-2xl text-[#262262] focus:outline-none focus:ring-4 focus:ring-[#3B4EFC]/10 transition-all placeholder:text-[rgba(0,0,0,0.4)]">

                            <template x-if="errors.new_password">
                                <p class="text-red-500 mt-2 md:mt-4 text-lg md:text-xl font-bold text-center" x-text="errors.new_password"></p>
                            </template>
                        </div>

                        <button @click="resetPassword()" :disabled="loading"
                                class="w-full bg-[#3B4EFC] text-white py-4 md:py-6 rounded-xl text-2xl md:text-3xl font-black hover:bg-[#262262] transition-all shadow-xl flex items-center justify-center gap-4">
                            <span x-show="!loading">Reset Password</span>
                            <span x-show="loading" class="animate-spin border-4 border-white/30 border-t-white rounded-full w-8 h-8 md:w-10 md:h-10"></span>
                        </button>
                    </div>
                </div>

            </div>

            {{-- Footer Text --}}
            <div class="mt-8 md:mt-12 text-center" x-show="step === 'login-email'">
                <p class="text-lg md:text-xl text-white font-medium bg-[#262262]/20 backdrop-blur-sm px-6 py-3 md:px-8 md:py-4 rounded-full">
                    Fixed returns, no price swings
                </p>
            </div>
        </div>
    </div>
</div>

<style>
[x-cloak] { display: none !important; }
</style>

@push('scripts')
<script>
function loginForm() {
    return {
        step: 'login-email',
        loading: false,
        resendTimer: 0,
        resendInterval: null,
        formData: {
            email: '',
            password: '',
            code: '',
            remember: false,
            forgot_code: '',
            new_password: '',
            new_password_confirmation: ''
        },
        errors: {},
        redirectUrl: '',

        maskEmail(email) {
            if (!email || !email.includes('@')) return email;
            const [user, domain] = email.split('@');
            return user.substring(0, 3) + '***@' + domain;
        },

        togglePassword(el) {
            const input = el.parentElement.querySelector('input');
            if (input.type === 'password') {
                input.type = 'text';
                el.querySelector('img').style.opacity = '1';
            } else {
                input.type = 'password';
                el.querySelector('img').style.opacity = '0.7';
            }
        },

        goBack() {
            if (this.step === 'login-password') this.step = 'login-email';
            else if (this.step === 'login-2fa') this.step = 'login-password';
            else if (this.step === 'forgot-email') this.step = 'login-password';
            else if (this.step === 'forgot-otp') this.step = 'forgot-email';
            else if (this.step === 'forgot-password') this.step = 'forgot-otp';
        },

        startResendTimer() {
            this.resendTimer = 60;
            if (this.resendInterval) clearInterval(this.resendInterval);
            this.resendInterval = setInterval(() => {
                if (this.resendTimer > 0) this.resendTimer--;
                else {
                    clearInterval(this.resendInterval);
                    this.resendInterval = null;
                }
            }, 1000);
        },

        async nextToPassword() {
            this.errors = {};
            if (!this.formData.email) {
                this.errors.email = 'Please enter your email or phone number';
                return;
            }
            // Simple validation
            if (this.formData.email.includes('@') && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.formData.email)) {
                this.errors.email = 'Invalid email format';
                return;
            }
            this.step = 'login-password';
        },

        async attemptLogin() {
            if (this.loading) return;
            this.loading = true;
            this.errors = {};
            try {
                const response = await fetch('{{ route('login.attempt') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        email: this.formData.email,
                        password: this.formData.password,
                        remember: this.formData.remember
                    })
                });

                const data = await response.json();

                if (data.success) {
                    if (data.two_fa_required) {
                        this.step = 'login-2fa';
                        this.startResendTimer();
                        window.showToast(data.message, 'success');
                    } else {
                        this.redirectUrl = data.redirect;
                        this.step = 'login-stay';
                    }
                } else {
                    this.errors.password = data.message || 'Invalid credentials';
                    window.showToast(this.errors.password, 'error');
                }
            } catch (e) {
                console.error(e);
                window.showToast('An error occurred. Please try again.', 'error');
            } finally {
                this.loading = false;
            }
        },

        async verify2FA() {
            if (this.loading) return;
            this.loading = true;
            this.errors = {};
            try {
                const response = await fetch('{{ route('login.verify-2fa') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ code: this.formData.code })
                });

                const data = await response.json();

                if (data.success) {
                    this.redirectUrl = data.redirect;
                    this.step = 'login-stay';
                    window.showToast(data.message, 'success');
                } else {
                    this.errors.code = data.message || 'Invalid code';
                    window.showToast(this.errors.code, 'error');
                }
            } catch (e) {
                window.showToast('An error occurred. Please try again.', 'error');
            } finally {
                this.loading = false;
            }
        },

        async resend2FA() {
            if (this.resendTimer > 0 || this.loading) return;
            this.loading = true;
            try {
                const response = await fetch('{{ route('login.resend-2fa') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    window.showToast(data.message, 'success');
                    this.startResendTimer();
                } else {
                    window.showToast(data.message, 'error');
                }
            } catch (e) {
                window.showToast('Failed to resend code', 'error');
            } finally {
                this.loading = false;
            }
        },

        completeLogin() {
            window.location.href = this.redirectUrl || '{{ route('cabinet.dashboard') }}';
        },

        // FORGOT PASSWORD LOGIC
        async sendForgotOTP() {
            if (this.loading) return;
            this.loading = true;
            this.errors = {};
            try {
                const response = await fetch('{{ route('password.send-code') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ email: this.formData.email })
                });
                const data = await response.json();
                if (data.success) {
                    if (data.code_sent) {
                        this.step = 'forgot-otp';
                        this.startResendTimer();
                        window.showToast(data.message, 'success');
                    } else {
                        window.showToast(data.message, 'info');
                    }
                } else {
                    window.showToast(data.message, 'error');
                }
            } catch (e) {
                window.showToast('Failed to send code', 'error');
            } finally {
                this.loading = false;
            }
        },

        async verifyForgotOTP() {
            if (this.loading) return;
            this.loading = true;
            this.errors = {};
            try {
                const response = await fetch('{{ route('password.verify-code') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ code: this.formData.forgot_code })
                });
                const data = await response.json();
                if (data.success) {
                    this.step = 'forgot-password';
                    window.showToast(data.message, 'success');
                } else {
                    this.errors.forgot_code = data.message;
                    window.showToast(data.message, 'error');
                }
            } catch (e) {
                window.showToast('Verification failed', 'error');
            } finally {
                this.loading = false;
            }
        },

        async resetPassword() {
            if (this.formData.new_password !== this.formData.new_password_confirmation) {
                this.errors.new_password = 'Passwords do not match';
                return;
            }
            if (this.loading) return;
            this.loading = true;
            try {
                const response = await fetch('{{ route('password.reset') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        password: this.formData.new_password,
                        password_confirmation: this.formData.new_password_confirmation
                    })
                });
                const data = await response.json();
                if (data.success) {
                    window.showToast(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect || '{{ route('cabinet.dashboard') }}';
                    }, 1000);
                } else {
                    window.showToast(data.message, 'error');
                }
            } catch (e) {
                window.showToast('Reset failed', 'error');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endpush
@endsection
