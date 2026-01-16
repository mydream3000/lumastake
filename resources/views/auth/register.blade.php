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
                <img src="{{ asset('assets/b759e555426373ac0810f48438900378efc8f6df.svg') }}" alt="Lumastake" class="w-12 h-12">
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('register') }}" class="space-y-4 mb-6">
                @csrf
                <input type="hidden" name="ref" value="{{ request('ref') }}">

                <!-- Name -->
                <div class="relative">
                    <input type="text" id="mobile-name" name="name" value="{{ old('name') }}" required
                           class="text-base w-full px-4 py-3 rounded-md bg-gray-100 border @error('name') border-red-500 @else border-gray-300 @enderror text-gray-600 placeholder-gray-400 focus:border-arbitex-orange focus:outline-none "
                           placeholder="Name">
                    @error('name')
                        <span class="text-xs text-red-600 mt-1" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Phone NEW-->
                <div class="relative" x-data="phoneInput('mobile')">

                    <div
                        class="flex w-full items-stretch rounded-md border border-gray-300 bg-gray-100
           overflow-hidden focus-within:border-arbitex-orange focus-within:ring-2
           focus-within:ring-arbitex-orange/30"
                    >

                        <button
                            type="button"
                            @click="open = !open"
                            class="text-base px-4 py-3 bg-gray-100 text-gray-700 flex items-center gap-2
             select-none"
                        >
                            <img :src="'https://flagcdn.com/w40/' + selectedCountry.code.toLowerCase() + '.png'" :alt="selectedCountry.name" class="w-5 h-auto">
                            <span x-text="selectedCountry.phone_code"></span>
                            <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>


                        <input type="tel" x-model="phone" @input="formatPhone" name="phone" maxlength="19" placeholder="123 456 7890" class="text-base px-4 py-3 flex-1 min-w-0 bg-transparent border-0 outline-none text-gray-600 placeholder-gray-400" required/>
                    </div>

{{--                   Выпадающий список стран (привязан к общему полю) --}}
                    <div
                        x-show="open"
                        @click.away="open = false"
                        class="absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-md
           shadow-lg max-h-80 overflow-y-auto"
                        style="display:none;"
                    >
                        <template x-for="country in countries" :key="country.code">
                            <button type="button" @click="selectCountry(country)" class="w-full px-4 py-3 text-left hover:bg-gray-100 flex items-center gap-3">
                                <img :src="'https://flagcdn.com/w40/' + country.code.toLowerCase() + '.png'" :alt="country.name" class="w-5 h-auto">
                                <span class="font-medium text-gray-700" x-text="country.phone_code"></span>
                                <span class="text-gray-600" x-text="country.name"></span>
                            </button>
                        </template>
                    </div>

<input type="hidden" name="country_code" x-model="selectedCountry.phone_code">
<input type="hidden" name="country" x-model="selectedCountry.code">
</div>


<!-- Email -->
<div class="relative">
<input type="email" id="mobile-email" name="email" value="{{ old('email') }}" required
     class="w-full px-4 py-3 rounded-md bg-gray-100 border @error('email') border-red-500 @else border-gray-300 @enderror text-gray-600 placeholder-gray-400 focus:border-arbitex-orange focus:outline-none text-base"
     placeholder="Email">
@error('email')
  <span class="text-xs text-red-600 mt-1" role="alert">{{ $message }}</span>
@enderror
</div>

<!-- Password -->
<div class="relative">
<input type="password" id="mobile-password" name="password" required
     class="w-full px-4 py-3 rounded-md bg-gray-100 border @error('password') border-red-500 @else border-gray-300 @enderror text-gray-600 placeholder-gray-400 focus:border-arbitex-orange focus:outline-none text-base"
     placeholder="Password">
@error('password')
  <span class="text-xs text-red-600 mt-1" role="alert">{{ $message }}</span>
@enderror
</div>

<!-- Confirm Password -->
<div class="relative">
<input type="password" id="mobile-password_confirmation" name="password_confirmation" required
     class="w-full px-4 py-3 rounded-md bg-gray-100 border border-gray-300 text-gray-600 placeholder-gray-400 focus:border-arbitex-orange focus:outline-none text-base"
     placeholder="Confirm Password">
</div>

<!-- Promo Code (Optional) -->
<div class="relative">
<input type="text" id="mobile-promo_code" name="promo_code" value="{{ old('promo_code') }}"
     class="w-full px-4 py-3 rounded-md bg-gray-100 border @error('promo_code') border-red-500 @else border-gray-300 @enderror text-gray-600 placeholder-gray-400 focus:border-arbitex-orange focus:outline-none text-base"
     placeholder="Promo Code (Optional)">
@error('promo_code')
  <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
@enderror
</div>

<!-- Account Type -->
<div class="space-y-2">
    <label class="text-sm text-gray-600">Account Type</label>
    <div class="grid grid-cols-2 gap-3">
        <label class="flex items-center gap-3 p-3 border border-gray-300 rounded-md cursor-pointer has-[:checked]:bg-arbitex-orange/10 has-[:checked]:border-arbitex-orange">
            <input type="radio" name="account_type" value="normal" class="w-4 h-4 text-arbitex-orange focus:ring-0 focus:outline-none" checked>
            <span class="font-medium text-sm text-gray-700">Normal</span>
        </label>
        <label class="flex items-center gap-3 p-3 border border-gray-300 rounded-md cursor-pointer has-[:checked]:bg-arbitex-orange/10 has-[:checked]:border-arbitex-orange">
            <input type="radio" name="account_type" value="islamic" class="w-4 h-4 text-arbitex-orange focus:ring-0 focus:outline-none">
            <span class="font-medium text-sm text-gray-700">Islamic</span>
        </label>
    </div>
</div>

<!-- Terms -->
<div class="flex items-start gap-2 py-4">
<input id="mobile-terms" name="terms" type="checkbox" required class="h-4 w-4 mt-1" {{ old('terms') ? 'checked' : '' }} />
<label for="mobile-terms" class="text-sm text-gray-600">
  By registering, I consent to the Lumastake <a href="{{ route('terms') }}" class="font-medium" target="_blank" rel="noopener">Terms of Service</a> and <a href="{{ route('privacy') }}" class="font-medium" target="_blank" rel="noopener">Privacy Policy</a>.
</label>
</div>
@error('terms')
<p class="text-xs text-red-600">{{ $message }}</p>
@enderror

<button type="submit" class="w-full bg-arbitex-orange text-white py-3 rounded-md font-semibold text-lg hover:opacity-90 transition-opacity">
Register
</button>
</form>

<!-- Divider -->
<div class="flex items-center my-6">
<hr class="flex-1 border-gray-300">
<span class="px-4 text-gray-400 text-sm">or</span>
<hr class="flex-1 border-gray-300">
</div>

<!-- Google Sign In -->
<a href="/auth/google" id="mobile-google-signin" class="w-full flex items-center justify-center gap-3 py-3 mb-6 bg-white text-gray-700 hover:bg-gray-50 transition-colors border border-gray-300 rounded-md shadow-sm">
<svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
</svg>
<span class="text-sm font-medium">Sign in with Google</span>
</a>

<!-- Login Link -->
<div class="text-center text-sm text-gray-400">
Do have an account? <a href="{{ route('login') }}" class="text-arbitex-green font-semibold hover:underline">Login</a>
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
<div class="w-1/2 bg-white flex items-center justify-center p-12">
<div class="w-full max-w-md">
<!-- Logo -->
<div class="flex justify-center mb-12">
<img src="{{ asset('assets/b759e555426373ac0810f48438900378efc8f6df.svg') }}" alt="Lumastake" style="width: 93.05px; height: 80px;">
</div>

<!-- Form -->
<form method="POST" action="{{ route('register') }}" class="space-y-6 mb-8">
@csrf
<input type="hidden" name="ref" value="{{ request('ref') }}">

<!-- Name -->
<div class="relative">
  <input type="text" id="name" name="name" value="{{ old('name') }}" required
         class="w-full px-6 py-6 rounded-md bg-gray-100 border @error('name') border-red-500 @else border-gray-300 @enderror text-gray-600 placeholder-gray-400 focus:border-arbitex-orange focus:outline-none text-lg"
         placeholder="Name">
  @error('name')
      <span class="text-sm text-red-600 mt-1" role="alert">{{ $message }}</span>
  @enderror
</div>



<div class="relative" x-data="phoneInput('desktop')">
  <div class="flex items-stretch gap-0">

      <div class="relative shrink-0">
          <button
              type="button"
              @click="open = !open"
              class="py-6 w-32 px-4 rounded-l-md bg-gray-100 border border-gray-300 border-r-0
text-gray-600 focus:border-arbitex-orange focus:outline-none text-lg
flex items-center justify-between gap-2"
          >
              <div class="flex items-center gap-2">
                  <img :src="'https://flagcdn.com/w40/' + selectedCountry.code.toLowerCase() + '.png'" :alt="selectedCountry.name" class="w-5 h-auto">
                  <span x-text="selectedCountry.phone_code"></span>
              </div>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
          </button>


          <div
              x-show="open"
              @click.away="open = false"
              class="absolute z-50 mt-1 w-[22rem] max-w-[calc(100vw-2rem)]
bg-white border border-gray-300 rounded-md shadow-lg max-h-80 overflow-y-auto"
              style="display:none;"
          >
              <template x-for="country in countries" :key="country.code">
                  <button type="button" @click="selectCountry(country)" class="w-full px-4 py-3 text-left hover:bg-gray-100 flex items-center gap-3">
                      <img :src="'https://flagcdn.com/w40/' + country.code.toLowerCase() + '.png'" :alt="country.name" class="w-5 h-auto">
                      <span class="font-medium text-gray-700" x-text="country.phone_code"></span>
                      <span class="text-gray-600" x-text="country.name"></span>
                  </button>
              </template>
          </div>
      </div>


      <input
          type="tel"
          x-model="phone"
          @input="formatPhone"
          name="phone"
          maxlength="19"
          required
          class="py-6 min-w-0 flex-1 px-6 rounded-r-md bg-gray-100 border border-gray-300 border-l-0
text-gray-600 placeholder-gray-400 focus:border-arbitex-orange focus:outline-none text-lg"
          placeholder="123 456 7890"
      />
  </div>

  <input type="hidden" name="country_code" x-model="selectedCountry.phone_code">
  <input type="hidden" name="country" x-model="selectedCountry.code">
</div>


<!-- Email -->
<div class="relative">
  <input type="email" id="email" name="email" value="{{ old('email') }}" required
         class="w-full px-6 py-6 rounded-md bg-gray-100 border @error('email') border-red-500 @else border-gray-300 @enderror text-gray-600 placeholder-gray-400 focus:border-arbitex-orange focus:outline-none text-lg"
         placeholder="Email">
  @error('email')
      <span class="text-sm text-red-600 mt-1" role="alert">{{ $message }}</span>
  @enderror
</div>

<!-- Password -->
<div class="relative">
  <input type="password" id="password" name="password" required
         class="w-full px-6 py-6 rounded-md bg-gray-100 border @error('password') border-red-500 @else border-gray-300 @enderror text-gray-600 placeholder-gray-400 focus:border-arbitex-orange focus:outline-none text-lg"
         placeholder="Password">
  @error('password')
      <span class="text-sm text-red-600 mt-1" role="alert">{{ $message }}</span>
  @enderror
</div>

<!-- Confirm Password -->
<div class="relative">
  <input type="password" id="password_confirmation" name="password_confirmation" required
         class="w-full px-6 py-6 rounded-md bg-gray-100 border border-gray-300 text-gray-600 placeholder-gray-400 focus:border-arbitex-orange focus:outline-none text-lg"
         placeholder="Confirm Password">
</div>

<!-- Promo Code (Optional) -->
<div class="relative">
  <input type="text" id="promo_code" name="promo_code" value="{{ old('promo_code') }}"
         class="w-full px-6 py-6 rounded-md bg-gray-100 border @error('promo_code') border-red-500 @else border-gray-300 @enderror text-gray-600 placeholder-gray-400 focus:border-arbitex-orange focus:outline-none text-lg"
         placeholder="Promo Code (Optional)">
  @error('promo_code')
      <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
  @enderror
</div>

<!-- Account Type -->
<div class="space-y-3">
    <label class="text-lg text-gray-600">Account Type</label>
    <div class="grid grid-cols-2 gap-4">
        <label class="register-in flex items-center gap-3 p-4 border border-cabinet-green rounded-lg cursor-pointer has-[:checked]:bg-cabinet-green/80 has-[:checked]:border-cabinet-green ">
            <input type="radio" name="account_type" value="normal" class="peer w-5 h-5 text-arbitex-orange focus:ring-0 focus:outline-none" checked>
            <span class="font-medium text-gray-700 peer-checked:text-white">Normal</span>
        </label>
        <label class="register-in flex items-center gap-3 p-4 border border-cabinet-green rounded-lg cursor-pointer has-[:checked]:bg-cabinet-green/80 has-[:checked]:border-cabinet-green ">
            <input type="radio" name="account_type" value="islamic" class="peer w-5 h-5 text-arbitex-orange focus:ring-0 focus:outline-none">
            <span class="font-medium text-gray-700 peer-checked:text-white">Islamic</span>
        </label>
    </div>
</div>

<!-- Terms -->
<div class="flex items-start gap-3 py-4">
   <input id="terms" name="terms" type="checkbox" required class="h-5 w-5 mt-1" {{ old('terms') ? 'checked' : '' }} />
   <label for="terms" class="text-gray-600 text-lg">
      By registering, I consent to the Lumastake <a href="{{ route('terms') }}" class="font-medium text-arbitex-green hover:underline" target="_blank" rel="noopener">Terms of Service</a> and <a href="{{ route('privacy') }}" class="font-medium text-arbitex-green hover:underline" target="_blank" rel="noopener">Privacy Policy</a>.
   </label>
</div>
@error('terms')
  <p class="text-sm text-red-600">{{ $message }}</p>
@enderror

<button type="submit" class="w-full bg-arbitex-orange text-white py-6 rounded-md font-semibold text-2xl hover:opacity-90 transition-opacity">
  Register
</button>
</form>

<!-- Divider -->
<div class="flex items-center mb-8">
<hr class="flex-1 border-gray-300">
<span class="px-6 text-gray-400 text-xl">or</span>
<hr class="flex-1 border-gray-300">
</div>

<!-- Google Sign In -->
<a href="/auth/google" id="desktop-google-signin" class="w-full flex items-center justify-center gap-4 py-4 mb-8 bg-white text-gray-700 hover:bg-gray-50 transition-colors border border-gray-300 rounded-md shadow-sm">
<svg class="w-6 h-6" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
</svg>
<span class="text-xl font-medium">Sign in with Google</span>
</a>

<!-- Login Link -->
<div class="text-center text-lg text-gray-400">
Do have an account? <a href="{{ route('login') }}" class="text-arbitex-green font-semibold hover:underline">Login</a>
</div>
</div>
</div>
</div>
</div>
@endsection

<!-- Email Verification Modal -->
<div x-data="verificationModal()"
x-show="showModal"
x-cloak
@show-verification-modal.window="showModal = true"
class="fixed inset-0 z-50 flex items-center justify-center"
style="display: none;">

<!-- Backdrop with opacity -->
<div x-show="showModal"
x-transition:enter="transition ease-out duration-300"
x-transition:enter-start="opacity-0"
x-transition:enter-end="opacity-100"
x-transition:leave="transition ease-in duration-200"
x-transition:leave-start="opacity-100"
x-transition:leave-end="opacity-0"
class="absolute inset-0 bg-black/50"
@click="closeModal"></div>

<!-- Modal Content -->
<div x-show="showModal"
x-transition:enter="transition ease-out duration-300 transform"
x-transition:enter-start="opacity-0 scale-95"
x-transition:enter-end="opacity-100 scale-100"
x-transition:leave="transition ease-in duration-200 transform"
x-transition:leave-start="opacity-100 scale-100"
x-transition:leave-end="opacity-0 scale-95"
class="relative z-10 bg-white rounded-2xl shadow-2xl w-full mx-4 md:mx-0 max-w-md md:max-w-lg p-8 md:p-12">

<!-- Icon -->
<div class="flex justify-center mb-6">
<div class="w-16 h-16 rounded-full bg-[#FEEAEA] flex items-center justify-center">
<svg class="w-8 h-8 text-arbitex-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
</svg>
</div>
</div>

<!-- Title -->
<h2 class="text-2xl md:text-3xl font-semibold text-center text-[#111928] mb-3">Verify Your Email</h2>

<!-- Description -->
<p class="text-center text-[#989898] text-sm md:text-base mb-8">
<span class="hidden md:inline">We've sent a 6-digit verification code</span>
<span class="md:hidden">Enter the 6 digit verification code</span>
</p>

<!-- Code Inputs -->
<div class="flex justify-center gap-2 md:gap-3 mb-3">
<template x-for="i in 6" :key="i">
<input
  type="text"
  maxlength="1"
  :ref="`input${i}`"
  x-model="code[i-1]"
  @input="handleInput($event, i)"
  @keydown.backspace="handleBackspace($event, i)"
  @paste="handlePaste($event)"
  class="w-10 h-12 md:w-12 md:h-14 text-center text-lg font-medium bg-[#f8f8f8] rounded-md border transition-colors focus:outline-none focus:border-arbitex-orange"
  :class="code[i-1] ? 'border-arbitex-orange text-arbitex-orange' : 'border-[rgba(68,68,68,0.6)] text-[#cccccc]'"
/>
</template>
</div>

<!-- Insert Button -->
<div class="flex justify-center mb-6">
<button type="button" @click="insertFromClipboard" class="px-4 py-1.5 bg-arbitex-green hover:bg-arbitex-green/90 text-white text-xs font-semibold rounded transition-colors">
Insert from Clipboard
</button>
</div>

<!-- Error Message -->
<div x-show="errorMessage"
x-transition
class="mb-4 text-center text-sm text-red-600">
<span x-text="errorMessage"></span>
</div>

<!-- Continue Button -->
<button
@click="verifyCode"
:disabled="loading"
class="w-full bg-arbitex-green text-white py-4 rounded font-bold text-lg hover:opacity-90 transition-opacity disabled:opacity-50 mb-4">
<span x-show="!loading">Continue</span>
<span x-show="loading">Verifying...</span>
</button>

<!-- Resend Code -->
<p class="text-center text-[#989898] text-xs md:text-sm">
Didn't receive the code?
<button
@click="resendCode"
:disabled="loading || resendTimer > 0"
class="font-medium hover:underline transition-colors"
:class="resendTimer > 0 ? 'text-gray-400 cursor-not-allowed' : 'text-arbitex-green'">
<span x-show="resendTimer === 0">Resend Code</span>
<span x-show="resendTimer > 0">Resend in <span x-text="resendTimer"></span>s</span>
</button>
</p>
</div>
</div>

@push('scripts')
<style>
/* Prevent blur on logo when modal is shown */
.min-h-screen img {
will-change: filter;
backface-visibility: hidden;
-webkit-backface-visibility: hidden;
transform: translateZ(0);
-webkit-transform: translateZ(0);
}
</style>
<script>
// Save account type before redirecting to Google OAuth
document.addEventListener('DOMContentLoaded', function() {
    const mobileGoogleBtn = document.getElementById('mobile-google-signin');
    const desktopGoogleBtn = document.getElementById('desktop-google-signin');

    function saveAccountTypeAndRedirect(e) {
        e.preventDefault();
        const accountType = document.querySelector('input[name="account_type"]:checked')?.value || 'normal';
        const targetUrl = e.target.closest('a').href;

        console.log('Saving account type before Google OAuth:', accountType);

        // Save to session via AJAX before redirect
        fetch('{{ route('register.save-account-type') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ account_type: accountType })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Account type saved successfully:', data);
            // Add small delay to ensure session is persisted
            setTimeout(() => {
                window.location.href = targetUrl;
            }, 100);
        })
        .catch(error => {
            console.error('Failed to save account type:', error);
            // Even if save fails, proceed with OAuth
            window.location.href = targetUrl;
        });
    }

    if (mobileGoogleBtn) {
        mobileGoogleBtn.addEventListener('click', saveAccountTypeAndRedirect);
    }
    if (desktopGoogleBtn) {
        desktopGoogleBtn.addEventListener('click', saveAccountTypeAndRedirect);
    }
});

function phoneInput(prefix) {
return {
open: false,
phone: '{{ old('phone', '') }}',
countries: [],
selectedCountry: { code: 'US', name: 'United States', phone_code: '+1' },

async init() {
// Load all countries first
try {
const response = await fetch('/api/v1/geoip/countries');
const data = await response.json();
if (data.success) {
this.countries = data.countries;
}
} catch (error) {
console.error('Failed to load countries:', error);
}

// Then get country by IP (only after countries are loaded)
if (this.countries.length > 0) {
try {
const response = await fetch('/api/v1/geoip/country');
const data = await response.json();
if (data.success && data.country) {
const country = this.countries.find(c => c.code === data.country.country_code);
if (country) {
  this.selectedCountry = country;
}
}
} catch (error) {
console.error('Failed to detect country:', error);
}
}
},

selectCountry(country) {
this.selectedCountry = country;
this.open = false;
},

formatPhone(event) {
// Удаляем все кроме цифр
let value = event.target.value.replace(/\D/g, '');

// Ограничиваем максимум 15 цифрами
if (value.length > 15) {
value = value.slice(0, 15);
}

// Форматируем с пробелами (группы по 3 цифры)
let formatted = '';
for (let i = 0; i < value.length; i++) {
if (i > 0 && i % 3 === 0) {
formatted += ' ';
}
formatted += value[i];
}

this.phone = formatted;
}
}
}

function verificationModal() {
return {
showModal: {{ session('show_verification_modal') ? 'true' : 'false' }},
code: ['', '', '', '', '', ''],
loading: false,
errorMessage: '',
resendTimer: 0,
resendInterval: null,

getInput(name) {
const ref = this.$refs[name];
return Array.isArray(ref) ? ref[0] : ref;
},

init() {
console.log('Verification modal initialized, showModal:', this.showModal);
if (this.showModal) {
this.$nextTick(() => {
const firstInput = this.getInput('input1');
if (firstInput) {
  firstInput.focus();
}
});
}
},

startResendTimer() {
this.resendTimer = 60; // 60 seconds cooldown
this.resendInterval = setInterval(() => {
this.resendTimer--;
if (this.resendTimer <= 0) {
clearInterval(this.resendInterval);
this.resendInterval = null;
}
}, 1000);
},

handleInput(event, index) {
const value = event.target.value;

if (value && /^[0-9]$/.test(value)) {
this.code[index - 1] = value;
this.errorMessage = '';

// Move to next input
if (index < 6) {
const nextInput = this.getInput(`input${index + 1}`);
if (nextInput) nextInput.focus();
}

// Auto submit if all filled
if (index === 6 && this.code.every(c => c)) {
this.verifyCode();
}
} else {
event.target.value = '';
this.code[index - 1] = '';
}
},

handleBackspace(event, index) {
if (!this.code[index - 1] && index > 1) {
event.preventDefault();
const prevInput = this.getInput(`input${index - 1}`);
if (prevInput) prevInput.focus();
}
},

handlePaste(event) {
event.preventDefault();
const paste = (event.clipboardData || window.clipboardData).getData('text');
const digits = paste.replace(/\D/g, '').slice(0, 6).split('');

digits.forEach((digit, i) => {
this.code[i] = digit;
});

const nextEmpty = digits.length < 6 ? digits.length + 1 : 6;
const nextInput = this.getInput(`input${nextEmpty}`);
if (nextInput) nextInput.focus();

if (digits.length === 6) {
this.verifyCode();
}
},

async insertFromClipboard() {
try {
const text = await navigator.clipboard.readText();
const digits = text.replace(/\D/g, '').slice(0, 6).split('');

if (digits.length !== 6) {
window.showToast('Please copy a valid 6-digit code', 'error');
return;
}

digits.forEach((digit, i) => {
this.code[i] = digit;
});

const lastInput = this.getInput('input6');
if (lastInput) lastInput.focus();

window.showToast('Code inserted from clipboard', 'success');
} catch (err) {
window.showToast('Failed to read clipboard. Please try again.', 'error');
}
},

async verifyCode() {
const codeString = this.code.join('');

if (codeString.length !== 6) {
this.errorMessage = 'Please enter all 6 digits';
return;
}

this.loading = true;
this.errorMessage = '';

try {
const response = await fetch('{{ route('register.verify-email') }}', {
method: 'POST',
headers: {
  'Content-Type': 'application/json',
  'X-CSRF-TOKEN': '{{ csrf_token() }}'
},
body: JSON.stringify({ code: codeString })
});

const data = await response.json();

if (data.success) {
window.location.href = data.redirect;
} else {
this.errorMessage = data.message || 'Verification failed';
this.code = ['', '', '', '', '', ''];
const firstInput = this.getInput('input1');
if (firstInput) firstInput.focus();
}
} catch (error) {
this.errorMessage = 'An error occurred. Please try again.';
} finally {
this.loading = false;
}
},

async resendCode() {
if (this.resendTimer > 0) return;

this.loading = true;
this.errorMessage = '';

try {
const response = await fetch('{{ route('register.resend-code') }}', {
method: 'POST',
headers: {
  'Content-Type': 'application/json',
  'X-CSRF-TOKEN': '{{ csrf_token() }}'
}
});

const data = await response.json();

if (data.success) {
this.errorMessage = '';
// Show success message
const successMsg = document.createElement('div');
successMsg.className = 'fixed top-4 right-4 bg-arbitex-green text-white px-6 py-3 rounded-lg shadow-lg z-50';
successMsg.textContent = 'New verification code sent! Please check your email.';
document.body.appendChild(successMsg);
setTimeout(() => successMsg.remove(), 5000);

// Start cooldown timer
this.startResendTimer();

// Clear current code
this.code = ['', '', '', '', '', ''];
const firstInput = this.getInput('input1');
if (firstInput) firstInput.focus();
} else {
this.errorMessage = data.message || 'Failed to resend code';
}
} catch (error) {
this.errorMessage = 'An error occurred. Please try again.';
} finally {
this.loading = false;
}
},

closeModal() {
// Don't allow closing by clicking outside
}
}
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endpush
