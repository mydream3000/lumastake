@extends('layouts.public')

@section('content')
<div class="min-h-screen flex">
    <!-- Mobile Header - only visible on mobile -->
    <div class="md:hidden w-full">
        <!-- Welcome Header -->
        <div class="bg-arbitex-dark px-6 py-4">
            <p class="text-white text-lg">
                Welcome to Lumastake <span class="font-bold text-arbitex-orange">Dashboard</span>
            </p>
        </div>

        <!-- Mobile Content -->
        <div class="bg-white min-h-screen p-6 text-gray-900">
            <!-- Logo -->
            <div class="flex justify-center mb-8">
                <img src="{{ asset('assets/fd7a3493a8091e0d436e86f812e64418b996048e.png') }}" alt="Lumastake" class="w-25">
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4 mb-6">
                @csrf
                <div class="relative">
                    <input type="email" id="mobile-email" name="email" value="{{ old('email') }}" required
                           class="text-base w-full px-4 py-3 rounded-md bg-gray-100 border @error('email') border-red-500 @else border-gray-300 @enderror text-gray-600 placeholder-gray-400 focus:border-arbitex-orange focus:outline-none"
                           placeholder="Email">
                    @error('email')
                        <span class="text-xs text-red-600 mt-1" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <div class="relative">
                    <input type="password" id="mobile-password" name="password" required
                           class="text-base w-full px-4 py-3 rounded-md bg-gray-100 border @error('password') border-red-500 @else border-gray-300 @enderror text-gray-600 placeholder-gray-400 focus:border-arbitex-orange focus:outline-none"
                           placeholder="Password">
                     @error('password')
                        <span class="text-xs text-red-600 mt-1" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <input id="remember" name="remember" type="checkbox" class="h-4 w-4" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember" class="text-sm">Remember me</label>
                    </div>
                    <button type="button" onclick="openForgotPasswordModal()" class="text-arbitex-orange text-sm hover:underline">Forgot Password?</button>
                </div>

                @if ($errors->has('auth'))
                    <div class="text-sm text-red-600">{{ $errors->first('auth') }}</div>
                @endif

                <button type="submit" class="w-full bg-arbitex-orange text-white py-3 rounded-md font-semibold text-lg hover:opacity-90 transition-opacity">
                    Login
                </button>
            </form>

            <!-- Divider -->
            <div class="flex items-center my-6">
                <hr class="flex-1 border-gray-300">
                <span class="px-4 text-gray-400 text-sm">or</span>
                <hr class="flex-1 border-gray-300">
            </div>

            <!-- Google Sign In -->
            <a href="/auth/google" class="w-full flex items-center justify-center gap-3 py-3 mb-6 bg-white text-gray-700 hover:bg-gray-50 transition-colors border border-gray-300 rounded-md shadow-sm">
                <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                <span class="text-sm font-medium">Sign in with Google</span>
            </a>

            <!-- Register Link -->
            <div class="text-center text-sm text-gray-400">
                Don't have an account? <a href="{{ route('register') }}" class="text-arbitex-green font-semibold hover:underline">Register</a>
            </div>
        </div>
    </div>

    <!-- Desktop Layout - hidden on mobile -->
    <div class="hidden md:flex w-full container-fixed">
        <!-- Left Panel -->
        <div class="relative w-1/2 bg-arbitex-dark overflow-hidden">
            <!-- Background Image -->
            <div class="absolute inset-0 opacity-70">
                <img src="{{ asset('assets/login_bg.jpg') }}" alt="Growth" class="w-full h-full object-cover">
            </div>

            <!-- Content -->
            <div class="relative z-10 flex flex-col justify-center h-full px-12">
                <h1 class="text-4xl lg:text-5xl font-normal text-white mb-2">Welcome to Lumastake</h1>
                <h2 class="text-5xl lg:text-6xl font-bold text-arbitex-orange">Dashboard</h2>
            </div>
        </div>

        <!-- Right Panel -->
        <div class="w-1/2 bg-white flex items-center justify-center p-12 text-gray-900">
            <div class="w-full max-w-md">
                <!-- Logo -->
                <div class="flex justify-center mb-12">
                    <img src="{{ asset('assets/fd7a3493a8091e0d436e86f812e64418b996048e.png') }}" alt="Lumastake" class="h-12">
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-6 mb-8">
                    @csrf
                    <div class="relative">
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-6 py-6 rounded-md bg-gray-100 border @error('email') border-red-500 @else border-gray-300 @enderror text-gray-600 placeholder-gray-400 focus:border-arbitex-orange focus:outline-none text-lg"
                               placeholder="Email">
                        @error('email')
                            <span class="text-sm text-red-600 mt-1" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="relative">
                        <input type="password" id="password" name="password" required
                               class="w-full px-6 py-6 rounded-md bg-gray-100 border @error('password') border-red-500 @else border-gray-300 @enderror text-gray-600 placeholder-gray-400 focus:border-arbitex-orange focus:outline-none text-lg"
                               placeholder="Password">
                        @error('password')
                            <span class="text-sm text-red-600 mt-1" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <input id="desktop-remember" name="remember" type="checkbox" class="h-5 w-5" {{ old('remember') ? 'checked' : '' }}>
                            <label for="desktop-remember" class="text-lg">Remember me</label>
                        </div>
                        <button type="button" onclick="openForgotPasswordModal()" class="text-arbitex-orange text-lg hover:underline">Forgot Password?</button>
                    </div>

                     @if ($errors->has('auth'))
                        <div class="text-sm text-red-600">{{ $errors->first('auth') }}</div>
                    @endif

                    <button type="submit" class="w-full bg-arbitex-orange text-white py-6 rounded-md font-semibold text-2xl hover:opacity-90 transition-opacity">
                        Login
                    </button>
                </form>

                <!-- Divider -->
                <div class="flex items-center mb-8">
                    <hr class="flex-1 border-gray-300">
                    <span class="px-6 text-gray-400 text-xl">or</span>
                    <hr class="flex-1 border-gray-300">
                </div>

                <!-- Google Sign In -->
                <a href="/auth/google" class="w-full flex items-center justify-center gap-4 py-4 mb-8 bg-white text-gray-700 hover:bg-gray-50 transition-colors border border-gray-300 rounded-md shadow-sm">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span class="text-xl font-medium">Sign in with Google</span>
                </a>

                <!-- Register Link -->
                <div class="text-center text-lg text-gray-400">
                    Don't have an account? <a href="{{ route('register') }}" class="text-arbitex-green font-semibold hover:underline">Register</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal - Step 1: Enter Email -->
    <div id="forgotPasswordModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md md:max-w-[518px] border border-[#ff451c] relative">
            <!-- Icon -->
            <div class="flex justify-center pt-8 md:pt-12">
                <div class="w-14 h-14 md:w-16 md:h-16 lg:w-20 lg:h-20 rounded-full bg-[#FEEAEA] flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/1131734522f9c2c89b063ed08de88f0758b97edf.svg') }}" alt="Forgot" class="w-7 h-7 md:w-8 md:h-8 lg:w-10 lg:h-10">
                </div>
            </div>

            <!-- Title -->
            <h2 class="text-center text-[#111928] font-medium text-lg md:text-2xl mt-6 md:mt-8 px-6">
                Forget Password?
            </h2>

            <!-- Description -->
            <p class="text-center text-[#989898] text-xs md:text-base mt-3 md:mt-6 px-6 leading-normal">
                No worries! Enter your email and we'll send you an OTP to reset your password.
            </p>

            <!-- Email Input -->
            <div class="px-6 md:px-16 mt-6 md:mt-8">
                <div class="relative">
                    <input type="email" id="forgotPasswordEmail"
                           placeholder="Enter Your Email"
                           class="w-full h-12 md:h-[42px] px-4 bg-[#f8f8f8] border border-[rgba(68,68,68,0.6)] rounded-md text-sm md:text-lg placeholder:text-[#111928] text-[#111928] focus:outline-none focus:border-arbitex-orange">
                    <img src="{{ asset('images/3cb93eda084d31a799a443eacb1e36a23ad3da3b.svg') }}" alt="Email" class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5">
                </div>
            </div>

            <!-- Send OTP Button -->
            <div class="px-6 md:px-16 mt-6 mb-8 md:mb-12">
                <button onclick="sendResetCode()" id="sendOtpBtn"
                        class="w-full bg-arbitex-green text-white font-bold py-3 md:py-2 rounded flex items-center justify-center gap-2 hover:opacity-90 transition-opacity disabled:opacity-50">
                    <span class="text-base md:text-lg">Send OTP</span>
                    <img src="{{ asset('images/a9bde09e6ca3f8164f1e2b82f70e63bf4b5552c9.svg') }}" alt="Arrow" class="w-3 h-2.5">
                </button>
            </div>
        </div>
    </div>

    <!-- Verify Code Modal - Step 2: Enter 6-digit Code -->
    <div id="verifyCodeModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md md:max-w-[518px] border border-[#ff451c] relative">
            <!-- Icon -->
            <div class="flex justify-center pt-8 md:pt-12">
                <div class="w-14 h-14 md:w-16 md:h-16 lg:w-20 lg:h-20 rounded-full bg-[#FEEAEA] flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/1131734522f9c2c89b063ed08de88f0758b97edf.svg') }}" alt="Code" class="w-7 h-7 md:w-8 md:h-8 lg:w-10 lg:h-10 object-contain">
                </div>
            </div>

            <!-- Title -->
            <h2 class="text-center text-[#111928] font-medium text-lg md:text-2xl mt-6 md:mt-8 px-6">
                Enter Verification Code
            </h2>

            <!-- Description -->
            <p class="text-center text-[#989898] text-xs md:text-base mt-3 md:mt-6 px-6 leading-normal">
                We've sent a 6-digit code to your email. Please enter it below.
            </p>

            <!-- Code Input - 6 separate boxes -->
            <div class="px-6 md:px-16 mt-6 md:mt-8">
                <div class="flex justify-center gap-2 md:gap-3">
                    <input type="text" inputmode="numeric" maxlength="1" id="code1" class="verify-code-input w-10 h-12 md:w-12 md:h-14 text-center text-lg md:text-xl font-medium bg-[#f8f8f8] rounded-md border border-[rgba(68,68,68,0.6)] text-[#8b0000] focus:outline-none focus:border-arbitex-orange transition-colors" />
                    <input type="text" inputmode="numeric" maxlength="1" id="code2" class="verify-code-input w-10 h-12 md:w-12 md:h-14 text-center text-lg md:text-xl font-medium bg-[#f8f8f8] rounded-md border border-[rgba(68,68,68,0.6)] text-[#8b0000] focus:outline-none focus:border-arbitex-orange transition-colors" />
                    <input type="text" inputmode="numeric" maxlength="1" id="code3" class="verify-code-input w-10 h-12 md:w-12 md:h-14 text-center text-lg md:text-xl font-medium bg-[#f8f8f8] rounded-md border border-[rgba(68,68,68,0.6)] text-[#8b0000] focus:outline-none focus:border-arbitex-orange transition-colors" />
                    <input type="text" inputmode="numeric" maxlength="1" id="code4" class="verify-code-input w-10 h-12 md:w-12 md:h-14 text-center text-lg md:text-xl font-medium bg-[#f8f8f8] rounded-md border border-[rgba(68,68,68,0.6)] text-[#8b0000] focus:outline-none focus:border-arbitex-orange transition-colors" />
                    <input type="text" inputmode="numeric" maxlength="1" id="code5" class="verify-code-input w-10 h-12 md:w-12 md:h-14 text-center text-lg md:text-xl font-medium bg-[#f8f8f8] rounded-md border border-[rgba(68,68,68,0.6)] text-[#8b0000] focus:outline-none focus:border-arbitex-orange transition-colors" />
                    <input type="text" inputmode="numeric" maxlength="1" id="code6" class="verify-code-input w-10 h-12 md:w-12 md:h-14 text-center text-lg md:text-xl font-medium bg-[#f8f8f8] rounded-md border border-[rgba(68,68,68,0.6)] text-[#8b0000] focus:outline-none focus:border-arbitex-orange transition-colors" />
                </div>
                <!-- Insert Button -->
                <div class="flex justify-center mt-3">
                    <button type="button" onclick="insertCodeFromClipboard('code', 6)" class="px-4 py-1.5 bg-arbitex-green hover:bg-arbitex-green/90 text-white text-xs font-semibold rounded transition-colors">
                        Insert from Clipboard
                    </button>
                </div>
            </div>

            <!-- Resend Code -->
            <div class="text-center mt-4 px-6">
                <p class="text-[#8b0000] text-xs md:text-sm">
                    Didn't receive the code?
                    <button onclick="resendResetCode()" id="resendBtn"
                            class="font-medium text-arbitex-green hover:underline transition-colors">
                        <span id="resendText">Resend Code</span>
                    </button>
                </p>
            </div>

            <!-- Verify Button -->
            <div class="px-6 md:px-16 mt-6 mb-8 md:mb-12">
                <button onclick="verifyResetCode()" id="verifyCodeBtn"
                        class="w-full bg-arbitex-green text-white font-bold py-3 md:py-2 rounded hover:opacity-90 transition-opacity disabled:opacity-50">
                    <span class="text-base md:text-lg">Verify Code</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Reset Password Modal - Step 3: Enter New Password -->
    <div id="resetPasswordModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md md:max-w-[518px] border border-[#ff451c] relative">
            <!-- Icon -->
            <div class="flex justify-center pt-8 md:pt-12">
                <div class="w-14 h-14 md:w-16 md:h-16 lg:w-20 lg:h-20 rounded-full bg-[#FEEAEA] flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-[#ff451c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
            </div>

            <!-- Title -->
            <h2 class="text-center text-[#111928] font-medium text-lg md:text-2xl mt-6 md:mt-8 px-6">
                Reset Password
            </h2>

            <!-- Description -->
            <p class="text-center text-[#989898] text-xs md:text-base mt-3 md:mt-6 px-6 leading-normal">
                Enter your new password must be different from previous used password.
            </p>

            <!-- New Password Input -->
            <div class="px-6 md:px-16 mt-6 md:mt-8">
                <input type="password" id="newPassword"
                       placeholder="Enter Password"
                       class="w-full h-12 md:h-[42px] px-4 bg-[#f8f8f8] border border-[rgba(68,68,68,0.6)] rounded-md text-sm md:text-lg placeholder:text-[#cccccc] text-[#8b0000] focus:outline-none focus:border-arbitex-orange">
            </div>

            <!-- Confirm Password Input -->
            <div class="px-6 md:px-16 mt-4">
                <input type="password" id="newPasswordConfirmation"
                       placeholder="Confirm Password"
                       class="w-full h-12 md:h-[42px] px-4 bg-[#f8f8f8] border border-[rgba(68,68,68,0.6)] rounded-md text-sm md:text-lg placeholder:text-[#cccccc] text-[#8b0000] focus:outline-none focus:border-arbitex-orange">
            </div>

            <!-- Reset Button -->
            <div class="px-6 md:px-16 mt-6 mb-8 md:mb-12">
                <button onclick="resetPassword()" id="resetPasswordBtn"
                        class="w-full bg-arbitex-green text-white font-bold py-3 md:py-2 rounded hover:opacity-90 transition-opacity disabled:opacity-50">
                    <span class="text-base md:text-lg">Reset Password</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 2FA Verification Modal -->
    <div id="twoFAModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md md:max-w-[518px] border border-[#ff451c] relative">
            <!-- Icon -->
            <div class="flex justify-center pt-8 md:pt-12">
                <div class="w-14 h-14 md:w-16 md:h-16 lg:w-20 lg:h-20 rounded-full bg-[#FEEAEA] flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/1131734522f9c2c89b063ed08de88f0758b97edf.svg') }}" alt="2FA" class="w-7 h-7 md:w-8 md:h-8 lg:w-10 lg:h-10 object-contain">
                </div>
            </div>

            <!-- Title -->
            <h2 class="text-center text-[#111928] font-medium text-lg md:text-2xl mt-6 md:mt-8 px-6">
                Enter the code to get access
            </h2>

            <!-- Description -->
            <p class="text-center text-[#989898] text-xs md:text-base mt-3 md:mt-6 px-6 leading-normal">
                We've sent a 6-digit verification code to your email. Please enter it below.
            </p>

            <!-- Code Input - 6 separate boxes -->
            <div class="px-6 md:px-16 mt-6 md:mt-8">
                <div class="flex justify-center gap-2 md:gap-3">
                    <input type="text" inputmode="numeric" maxlength="1" id="twofa_code1" class="twofa-code-input w-10 h-12 md:w-12 md:h-14 text-center text-lg md:text-xl font-medium bg-[#f8f8f8] rounded-md border border-[rgba(68,68,68,0.6)] text-[#8b0000] focus:outline-none focus:border-arbitex-orange transition-colors" />
                    <input type="text" inputmode="numeric" maxlength="1" id="twofa_code2" class="twofa-code-input w-10 h-12 md:w-12 md:h-14 text-center text-lg md:text-xl font-medium bg-[#f8f8f8] rounded-md border border-[rgba(68,68,68,0.6)] text-[#8b0000] focus:outline-none focus:border-arbitex-orange transition-colors" />
                    <input type="text" inputmode="numeric" maxlength="1" id="twofa_code3" class="twofa-code-input w-10 h-12 md:w-12 md:h-14 text-center text-lg md:text-xl font-medium bg-[#f8f8f8] rounded-md border border-[rgba(68,68,68,0.6)] text-[#8b0000] focus:outline-none focus:border-arbitex-orange transition-colors" />
                    <input type="text" inputmode="numeric" maxlength="1" id="twofa_code4" class="twofa-code-input w-10 h-12 md:w-12 md:h-14 text-center text-lg md:text-xl font-medium bg-[#f8f8f8] rounded-md border border-[rgba(68,68,68,0.6)] text-[#8b0000] focus:outline-none focus:border-arbitex-orange transition-colors" />
                    <input type="text" inputmode="numeric" maxlength="1" id="twofa_code5" class="twofa-code-input w-10 h-12 md:w-12 md:h-14 text-center text-lg md:text-xl font-medium bg-[#f8f8f8] rounded-md border border-[rgba(68,68,68,0.6)] text-[#8b0000] focus:outline-none focus:border-arbitex-orange transition-colors" />
                    <input type="text" inputmode="numeric" maxlength="1" id="twofa_code6" class="twofa-code-input w-10 h-12 md:w-12 md:h-14 text-center text-lg md:text-xl font-medium bg-[#f8f8f8] rounded-md border border-[rgba(68,68,68,0.6)] text-[#8b0000] focus:outline-none focus:border-arbitex-orange transition-colors" />
                </div>
                <!-- Insert Button -->
                <div class="flex justify-center mt-3">
                    <button type="button" onclick="insertCodeFromClipboard('twofa_code', 6)" class="px-4 py-1.5 bg-arbitex-green hover:bg-arbitex-green/90 text-white text-xs font-semibold rounded transition-colors">
                        Insert from Clipboard
                    </button>
                </div>
            </div>

            <!-- Resend Code -->
            <div class="text-center mt-4 px-6">
                <p class="text-[#8b0000] text-xs md:text-sm">
                    Didn't receive the code?
                    <button onclick="resend2FACode()" id="resend2FABtn"
                            class="font-medium text-arbitex-green hover:underline transition-colors">
                        <span id="resend2FAText">Resend Code</span>
                    </button>
                </p>
            </div>

            <!-- Verify Button -->
            <div class="px-6 md:px-16 mt-6 mb-8 md:mb-12">
                <button onclick="verify2FACode()" id="verify2FABtn"
                        class="w-full bg-arbitex-green text-white font-bold py-3 md:py-2 rounded hover:opacity-90 transition-opacity disabled:opacity-50">
                    <span class="text-base md:text-lg">Verify Code</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let resendTimer = 0;
    let resendInterval = null;

    // Universal function to insert 6-digit code from clipboard
    async function insertCodeFromClipboard(prefix, count) {
        try {
            const text = await navigator.clipboard.readText();
            const digits = text.replace(/[^0-9]/g, '').slice(0, count);

            if (digits.length !== count) {
                window.showToast(`Please copy a valid ${count}-digit code`, 'error');
                return;
            }

            // Fill all inputs
            for (let i = 0; i < count; i++) {
                const input = document.getElementById(`${prefix}${i + 1}`);
                if (input) {
                    input.value = digits[i];
                    input.classList.remove('border-[rgba(68,68,68,0.6)]');
                    input.classList.add('border-arbitex-orange');
                }
            }

            // Focus last input
            const lastInput = document.getElementById(`${prefix}${count}`);
            if (lastInput) lastInput.focus();

            window.showToast('Code inserted from clipboard', 'success');
        } catch (err) {
            window.showToast('Failed to read clipboard. Please try again.', 'error');
        }
    }

    // Open/Close Modal Functions
    function openForgotPasswordModal() {
        // Clear email field to avoid duplicates
        document.getElementById('forgotPasswordEmail').value = '';
        document.getElementById('forgotPasswordModal').classList.remove('hidden');
    }

    function closeForgotPasswordModal() {
        document.getElementById('forgotPasswordModal').classList.add('hidden');
    }

    function openVerifyCodeModal() {
        document.getElementById('verifyCodeModal').classList.remove('hidden');
        // Clear all code inputs
        for (let i = 1; i <= 6; i++) {
            document.getElementById(`code${i}`).value = '';
        }
        // Focus first input
        setTimeout(() => {
            document.getElementById('code1').focus();
        }, 100);
    }

    function closeVerifyCodeModal() {
        document.getElementById('verifyCodeModal').classList.add('hidden');
    }

    function openResetPasswordModal() {
        document.getElementById('resetPasswordModal').classList.remove('hidden');
    }

    function closeResetPasswordModal() {
        document.getElementById('resetPasswordModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('forgotPasswordModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeForgotPasswordModal();
    });

    document.getElementById('verifyCodeModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeVerifyCodeModal();
    });

    document.getElementById('resetPasswordModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeResetPasswordModal();
    });

    // Send Reset Code
    async function sendResetCode() {
        console.log('sendResetCode called');
        const btn = document.getElementById('sendOtpBtn');
        if (btn.disabled) {
            console.log('Button is disabled, returning');
            return;
        }

        const email = document.getElementById('forgotPasswordEmail').value;
        console.log('Email:', email);

        if (!email) {
            console.log('No email provided');
            window.showToast('Please enter your email', 'error');
            return;
        }

        btn.disabled = true;
        console.log('Sending request to server...');

        try {
            const response = await fetch('{{ route('password.send-code') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email })
            });

            console.log('Response status:', response.status);
            const data = await response.json();
            console.log('Response data:', data);
            console.log('Full response:', JSON.stringify(data, null, 2));

            if (data.success && data.code_sent === true) {
                console.log('Success! Code sent, showing toast and opening verify modal');
                window.showToast(data.message, 'success');
                console.log('Closing forgot password modal...');
                closeForgotPasswordModal();
                console.log('Opening verify code modal...');
                openVerifyCodeModal();
                startResendTimer();
            } else if (data.success && data.code_sent === false) {
                console.log('Email flow acknowledged, but code was not sent (likely no account). Staying on email step');
                window.showToast(data.message, 'info');
                // Do not move to next modal
            } else {
                console.log('Error response:', data.message);
                window.showToast(data.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            window.showToast('An error occurred. Please try again.', 'error');
        } finally {
            btn.disabled = false;
        }
    }

    // Verify Reset Code
    async function verifyResetCode() {
        const btn = document.getElementById('verifyCodeBtn');
        if (btn.disabled) return;

        // Collect code from 6 separate inputs
        let code = '';
        for (let i = 1; i <= 6; i++) {
            code += document.getElementById(`code${i}`).value;
        }

        if (code.length !== 6) {
            window.showToast('Please enter all 6 digits', 'error');
            return;
        }

        btn.disabled = true;

        try {
            const response = await fetch('{{ route('password.verify-code') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ code })
            });

            const data = await response.json();

            if (data.success) {
                window.showToast(data.message, 'success');
                closeVerifyCodeModal();
                openResetPasswordModal();
            } else {
                window.showToast(data.message, 'error');
            }
        } catch (error) {
            window.showToast('An error occurred. Please try again.', 'error');
        } finally {
            btn.disabled = false;
        }
    }

    // Reset Password
    async function resetPassword() {
        const btn = document.getElementById('resetPasswordBtn');
        if (btn.disabled) return;

        const password = document.getElementById('newPassword').value;
        const passwordConfirmation = document.getElementById('newPasswordConfirmation').value;

        if (!password || !passwordConfirmation) {
            window.showToast('Please fill in all fields', 'error');
            return;
        }

        if (password !== passwordConfirmation) {
            window.showToast('Passwords do not match', 'error');
            return;
        }

        btn.disabled = true;

        try {
            const response = await fetch('{{ route('password.reset') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    password,
                    password_confirmation: passwordConfirmation
                })
            });

            const data = await response.json();

            if (data.success) {
                window.showToast(data.message, 'success');
                closeResetPasswordModal();
                document.getElementById('newPassword').value = '';
                document.getElementById('newPasswordConfirmation').value = '';
                // Redirect to cabinet after successful reset
                const redirectUrl = data.redirect || '{{ route('cabinet.dashboard') }}';
                setTimeout(() => {
                    window.location.href = redirectUrl;
                }, 800);
            } else {
                window.showToast(data.message, 'error');
            }
        } catch (error) {
            window.showToast('An error occurred. Please try again.', 'error');
        } finally {
            btn.disabled = false;
        }
    }

    // Resend Reset Code
    async function resendResetCode() {
        const btn = document.getElementById('resendBtn');
        if (btn.disabled || resendTimer > 0) return;

        btn.disabled = true;

        try {
            const response = await fetch('{{ route('password.resend-code') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await response.json();

            if (data.success) {
                window.showToast(data.message, 'success');
                startResendTimer();
            } else {
                window.showToast(data.message, 'error');
            }
        } catch (error) {
            window.showToast('An error occurred. Please try again.', 'error');
        } finally {
            btn.disabled = false;
        }
    }

    // Start Resend Timer
    function startResendTimer() {
        resendTimer = 60;
        const btn = document.getElementById('resendBtn');
        const text = document.getElementById('resendText');

        btn.disabled = true;
        btn.classList.add('text-gray-400', 'cursor-not-allowed');
        btn.classList.remove('text-arbitex-green');

        if (resendInterval) clearInterval(resendInterval);

        resendInterval = setInterval(() => {
            resendTimer--;
            text.textContent = `Resend in ${resendTimer}s`;

            if (resendTimer <= 0) {
                clearInterval(resendInterval);
                resendInterval = null;
                btn.disabled = false;
                btn.classList.remove('text-gray-400', 'cursor-not-allowed');
                btn.classList.add('text-arbitex-green');
                text.textContent = 'Resend Code';
            }
        }, 1000);
    }

    // Code Input Auto-focus and Paste Handling
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.verify-code-input');

        inputs.forEach((input, index) => {
            // Auto-focus next input on typing
            input.addEventListener('input', function(e) {
                const value = e.target.value;

                // Only allow numbers
                if (!/^\d*$/.test(value)) {
                    e.target.value = '';
                    return;
                }

                // Move to next input if value entered
                if (value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }

                // Change border color when filled
                if (value.length === 1) {
                    e.target.classList.remove('border-[rgba(68,68,68,0.6)]');
                    e.target.classList.add('border-arbitex-orange');
                } else {
                    e.target.classList.add('border-[rgba(68,68,68,0.6)]');
                    e.target.classList.remove('border-arbitex-orange');
                }
            });

            // Handle backspace - move to previous input
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            // Handle paste - distribute characters across inputs
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pasteData = e.clipboardData.getData('text').trim();

                // Only allow 6 digits
                if (!/^\d{6}$/.test(pasteData)) {
                    window.showToast('Please paste a valid 6-digit code', 'error');
                    return;
                }

                // Distribute digits across inputs
                for (let i = 0; i < 6; i++) {
                    inputs[i].value = pasteData[i];
                    inputs[i].classList.remove('border-[rgba(68,68,68,0.6)]');
                    inputs[i].classList.add('border-arbitex-orange');
                }

                // Focus last input
                inputs[5].focus();
            });
        });
    });

    // 2FA Modal Logic
    @if(session('show_2fa_modal'))
        document.addEventListener('DOMContentLoaded', function() {
            open2FAModal();
        });
    @endif

    function open2FAModal() {
        document.getElementById('twoFAModal').classList.remove('hidden');
        // Clear all code inputs
        for (let i = 1; i <= 6; i++) {
            document.getElementById(`twofa_code${i}`).value = '';
        }
        // Focus first input
        setTimeout(() => {
            document.getElementById('twofa_code1').focus();
        }, 100);
    }

    function close2FAModal() {
        document.getElementById('twoFAModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('twoFAModal')?.addEventListener('click', function(e) {
        if (e.target === this) close2FAModal();
    });

    // Verify 2FA Code
    async function verify2FACode() {
        const btn = document.getElementById('verify2FABtn');
        if (btn.disabled) return;

        // Collect code from 6 separate inputs
        let code = '';
        for (let i = 1; i <= 6; i++) {
            code += document.getElementById(`twofa_code${i}`).value;
        }

        if (code.length !== 6) {
            window.showToast('Please enter all 6 digits', 'error');
            return;
        }

        btn.disabled = true;

        try {
            const response = await fetch('{{ route('login.verify-2fa') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ code })
            });

            const data = await response.json();

            if (data.success) {
                window.showToast(data.message, 'success');
                close2FAModal();
                // Redirect to cabinet
                const redirectUrl = data.redirect || '{{ route('cabinet.dashboard') }}';
                setTimeout(() => {
                    window.location.href = redirectUrl;
                }, 500);
            } else {
                window.showToast(data.message, 'error');
            }
        } catch (error) {
            window.showToast('An error occurred. Please try again.', 'error');
        } finally {
            btn.disabled = false;
        }
    }

    // Resend 2FA Code
    let twofa_resendTimer = 0;
    let twofa_resendInterval = null;

    async function resend2FACode() {
        const btn = document.getElementById('resend2FABtn');
        if (btn.disabled || twofa_resendTimer > 0) return;

        btn.disabled = true;

        try {
            const response = await fetch('{{ route('login.resend-2fa') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await response.json();

            if (data.success) {
                window.showToast(data.message, 'success');
                start2FAResendTimer();
            } else {
                window.showToast(data.message, 'error');
            }
        } catch (error) {
            window.showToast('An error occurred. Please try again.', 'error');
        } finally {
            btn.disabled = false;
        }
    }

    // Start 2FA Resend Timer
    function start2FAResendTimer() {
        twofa_resendTimer = 60;
        const btn = document.getElementById('resend2FABtn');
        const text = document.getElementById('resend2FAText');

        btn.disabled = true;
        btn.classList.add('text-gray-400', 'cursor-not-allowed');
        btn.classList.remove('text-arbitex-green');

        if (twofa_resendInterval) clearInterval(twofa_resendInterval);

        twofa_resendInterval = setInterval(() => {
            twofa_resendTimer--;
            text.textContent = `Resend in ${twofa_resendTimer}s`;

            if (twofa_resendTimer <= 0) {
                clearInterval(twofa_resendInterval);
                twofa_resendInterval = null;
                btn.disabled = false;
                btn.classList.remove('text-gray-400', 'cursor-not-allowed');
                btn.classList.add('text-arbitex-green');
                text.textContent = 'Resend Code';
            }
        }, 1000);
    }

    // 2FA Code Input Auto-focus and Paste Handling
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.twofa-code-input');

        inputs.forEach((input, index) => {
            // Auto-focus next input on typing
            input.addEventListener('input', function(e) {
                const value = e.target.value;

                // Only allow numbers
                if (!/^\d*$/.test(value)) {
                    e.target.value = '';
                    return;
                }

                // Move to next input if value entered
                if (value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }

                // Change border color when filled
                if (value.length === 1) {
                    e.target.classList.remove('border-[rgba(68,68,68,0.6)]');
                    e.target.classList.add('border-arbitex-orange');
                } else {
                    e.target.classList.add('border-[rgba(68,68,68,0.6)]');
                    e.target.classList.remove('border-arbitex-orange');
                }
            });

            // Handle backspace - move to previous input
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            // Handle paste - distribute characters across inputs
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pasteData = e.clipboardData.getData('text').trim();

                // Only allow 6 digits
                if (!/^\d{6}$/.test(pasteData)) {
                    window.showToast('Please paste a valid 6-digit code', 'error');
                    return;
                }

                // Distribute digits across inputs
                for (let i = 0; i < 6; i++) {
                    inputs[i].value = pasteData[i];
                    inputs[i].classList.remove('border-[rgba(68,68,68,0.6)]');
                    inputs[i].classList.add('border-arbitex-orange');
                }

                // Focus last input
                inputs[5].focus();
            });
        });
    });
</script>
@endsection
