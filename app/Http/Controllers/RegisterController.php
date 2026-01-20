<?php

namespace App\Http\Controllers;

use App\Services\TelegramBotService;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\PromoCode;
use App\Models\Transaction;
use App\Mail\EmailVerificationCode;
use Illuminate\Validation\Rule;

class RegisterController extends BaseController
{
    public function submit(Request $request, TelegramBotService $telegramBotService)
    {
        // First, check if email exists but is not verified
        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser && !$existingUser->email_verified_at) {
            // User exists but not verified - allow re-registration
            Log::info('Existing unverified user found, will update', [
                'email' => $existingUser->email,
                'user_id' => $existingUser->id,
            ]);
        }

        // Normalize phone and country code before validation
        $rawPhone = (string) $request->input('phone', '');
        $rawCountryCode = (string) $request->input('country_code', '');
        $normalizedCountryCode = $rawCountryCode;
        // Ensure country_code is in "+<digits>" format if possible
        if ($normalizedCountryCode !== '' && $normalizedCountryCode[0] !== '+') {
            if (preg_match('/^\d{1,4}$/', $normalizedCountryCode)) {
                $normalizedCountryCode = '+' . $normalizedCountryCode;
            }
        }
        $digitsPhone = preg_replace('/\D+/', '', $rawPhone);
        // If phone starts with dial-code digits, strip them
        $dialDigits = preg_replace('/\D+/', '', $normalizedCountryCode);
        if ($dialDigits && str_starts_with($digitsPhone, $dialDigits)) {
            $digitsPhone = substr($digitsPhone, strlen($dialDigits));
        }
        $request->merge([
            'phone' => $digitsPhone,
            'country_code' => $normalizedCountryCode,
        ]);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['required', 'regex:/^\d{3,20}$/'],
            'country_code' => ['required', 'regex:/^\+\d{1,4}$/'],
            'country' => ['nullable', 'string', 'max:2'],
            'email' => $existingUser && !$existingUser->email_verified_at
                ? 'required|email'
                : 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'promo_code' => 'nullable|string|max:64',
            'ref' => 'nullable|string|max:36',
            'terms' => 'accepted',
            'account_type' => ['required', Rule::in(['normal', 'islamic'])],
        ], [
            'terms.accepted' => 'You must accept the Terms and Privacy.',
            'phone.required' => 'Phone number is required.',
            'phone.regex' => 'Phone must contain only digits (3-20).',
            'country_code.required' => 'Country code is required.',
            'country_code.regex' => 'Country code must be a dial code like +39.',
        ]);

        // Determine country from IP to ensure accuracy (requested feature)
        // Prioritize Cloudflare header, then GeoIP database
        $ipCountry = $request->header('CF-IPCountry');
        if (!$ipCountry) {
            $ipCountry = \App\Helpers\GeoIpHelper::getCountryCodeByIp($request->ip());
        }

        // If we detected a valid country code, use it
        if ($ipCountry && strlen($ipCountry) === 2) {
            $data['country'] = strtoupper($ipCountry);
        }

        $promo = null;
        if (!empty($data['promo_code'])) {
            $promo = PromoCode::query()->where('code', $data['promo_code'])->first();
            if (!$promo || !$promo->is_active || ($promo->max_uses > 0 && $promo->used_count >= $promo->max_uses)) {
                return back()->withErrors(['promo_code' => 'Promo code is invalid or has reached its limit.'])->withInput();
            }
        }

        $referrer = null;
        if (!empty($data['ref'])) {
            $referrer = User::query()->where('uuid', $data['ref'])->first();
        }

        $user = DB::transaction(function () use ($data, $promo, $referrer, $existingUser) {
            // Generate 6-digit verification code
            $verificationCode = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);

            Log::info('Generated verification code', [
                'code' => $verificationCode,
                'code_type' => gettype($verificationCode),
                'code_length' => strlen($verificationCode),
            ]);

            // Generate unique referral code
            $referralCode = null;
            for ($i = 0; $i < 5; $i++) {
                $candidate = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(8));
                if (!User::where('referral_code', $candidate)->exists()) {
                    $referralCode = $candidate;
                    break;
                }
            }
            $referralCode = $referralCode ?: \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(8));

            // Update existing user or create new
            if ($existingUser && !$existingUser->email_verified_at) {
                $existingUser->update([
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'country_code' => $data['country_code'],
                    'country' => $data['country'] ?? null,
                    'password' => $data['password'],
                    'account_type' => $data['account_type'],
                    'email_verification_code' => $verificationCode,
                    'email_verification_code_expires_at' => now()->addMinutes(15),
                    'referral_code' => $existingUser->referral_code ?: $referralCode,
                ]);

                $user = $existingUser;

                Log::info('Updated existing unverified user', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
            } else {
                // Create user
                $user = User::create([
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'country_code' => $data['country_code'],
                    'country' => $data['country'] ?? null,
                    'email' => $data['email'],
                    'password' => $data['password'], // hashed by cast
                    'account_type' => $data['account_type'],
                    'referred_by' => $referrer?->id,
                    'referral_code' => $referralCode,
                    'email_verification_code' => $verificationCode,
                    'email_verification_code_expires_at' => now()->addMinutes(15),
                ]);
            }

            Log::info('User created with code', [
                'user_id' => $user->id,
                'stored_code' => $user->email_verification_code,
                'stored_code_type' => gettype($user->email_verification_code),
            ]);

            // Apply promo bonus
            if ($promo && $promo->start_balance > 0) {
                $amount = (float)$promo->start_balance;

                Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'deposit',
                    'status' => 'confirmed',
                    'amount' => $amount,
                    'description' => 'Promo bonus',
                    'meta' => ['promo_code' => $promo->code],
                ]);

                $user->increment('balance', $amount);
                $user->increment('deposited', $amount);
                $promo->increment('used_count');
            }

            return $user;
        });

        // Send Telegram notification about new registration
        try {
            $telegramBotService->sendUserRegistration($user, $request->ip(), 'form');
        } catch (\Exception $e) {
            Log::error('Failed to send Telegram notification for new user registration', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Send verification email outside of transaction
        $emailSent = false;
        try {
            Log::info('Attempting to send verification email', [
                'email' => $user->email,
                'code' => $user->email_verification_code,
            ]);

            // Use failover mailer (smtp -> log) to avoid hard failures
            Mail::mailer('failover')->to($user->email)->send(new EmailVerificationCode(
                $user->email_verification_code,
                $user->name
            ));

            Log::info('Verification email sent successfully', [
                'email' => $user->email,
            ]);
            $emailSent = true;
        } catch (\Exception $e) {
            Log::error('Failed to send verification email', [
                'email' => $user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Don't fail registration if email fails — user can request resend later
        }

        // Store user ID in session but don't log in yet
        session([
            'pending_verification_user_id' => $user->id,
            'verification_email_sent' => $emailSent,
            'verification_email' => $user->email,
        ]);

        return redirect()->route('register')->with([
            'show_verification_modal' => true,
            'verification_email_sent' => $emailSent,
            'verification_email' => $user->email,
        ]);
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $userId = session('pending_verification_user_id');

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Verification session expired. Please register again.',
            ], 400);
        }

        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Debug logging
        Log::info('Email verification attempt', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'stored_code' => $user->email_verification_code,
            'input_code' => $request->code,
            'codes_match' => $user->email_verification_code === $request->code,
            'stored_code_type' => gettype($user->email_verification_code),
            'input_code_type' => gettype($request->code),
            'expires_at' => $user->email_verification_code_expires_at,
            'now' => now(),
        ]);

        // Check if code matches and not expired
        if ($user->email_verification_code !== $request->code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code.',
            ], 400);
        }

        if ($user->email_verification_code_expires_at < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Verification code has expired.',
            ], 400);
        }

        // Mark email as verified
        $user->email_verified_at = now();
        $user->email_verification_code = null;
        $user->email_verification_code_expires_at = null;
        $user->save();

        // Clear session and log in user
        session()->forget('pending_verification_user_id');
        Auth::login($user);

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully!',
            'redirect' => route('cabinet.dashboard'),
        ]);
    }

    public function resendCode(Request $request)
    {
        $userId = session('pending_verification_user_id');

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Verification session expired.',
            ], 400);
        }

        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Generate new code
        $verificationCode = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->email_verification_code = $verificationCode;
        $user->email_verification_code_expires_at = now()->addMinutes(15);
        $user->save();

        // Send verification email
        try {
            Log::info('Attempting to resend verification email', [
                'email' => $user->email,
                'code' => $verificationCode,
            ]);

            // Use failover mailer (smtp -> log) to avoid hard failures
            Mail::mailer('failover')->to($user->email)->send(new EmailVerificationCode($verificationCode, $user->name));

            Log::info('Verification email resent successfully', [
                'email' => $user->email,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Verification code resent successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to resend verification email', [
                'email' => $user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Respond with 200 to keep frontend UX stable, but indicate failure
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email. Please try again later.',
            ], 200);
        }
    }

    /**
     * Save account type to session before Google OAuth redirect
     */
    public function saveAccountType(Request $request)
    {
        $data = $request->validate([
            'account_type' => ['required', Rule::in(['normal', 'islamic'])],
        ]);

        // Save to session with explicit save to ensure persistence
        session(['pending_account_type' => $data['account_type']]);
        session()->save(); // Force save immediately

        Log::info('Account type saved to session before OAuth', [
            'account_type' => $data['account_type'],
            'session_id' => session()->getId(),
        ]);

        return response()->json([
            'success' => true,
            'account_type' => $data['account_type'],
        ]);
    }

    public function step1SendCode(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|unique:users,email',
        ]);

        $code = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store code in cache for 15 min
        \Illuminate\Support\Facades\Cache::put('reg_code_' . $data['email'], $code, now()->addMinutes(15));

        try {
            Mail::mailer('failover')->to($data['email'])->send(new EmailVerificationCode($code, 'User'));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Failed to send reg code', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to send email.']);
        }
    }

    public function step2VerifyCode(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        $cachedCode = \Illuminate\Support\Facades\Cache::get('reg_code_' . $data['email']);

        if ($cachedCode && $cachedCode === $data['code']) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid or expired code.']);
    }

    public function resendCodeApi(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
        ]);

        $code = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);
        \Illuminate\Support\Facades\Cache::put('reg_code_' . $data['email'], $code, now()->addMinutes(15));

        try {
            Mail::mailer('failover')->to($data['email'])->send(new EmailVerificationCode($code, 'User'));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to send email.']);
        }
    }

    public function finalize(Request $request, TelegramBotService $telegramBotService)
    {
        $data = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'name' => 'required|string|max:255',
            'account_type' => ['required', Rule::in(['normal', 'islamic'])],
            'promo_code' => 'nullable|string|max:64',
            'ref' => 'nullable|string|max:36',
            'nationality' => 'nullable|string|max:2',
            'gender' => 'nullable|string|in:male,female,other',
            'dob_dd' => 'nullable|string|max:2',
            'dob_mm' => 'nullable|string|max:2',
            'dob_yyyy' => 'nullable|string|max:4',
            'phone' => 'required|string|max:20',
            'country_code' => 'required|string|max:10',
        ]);

        // Verify that the email was actually verified (optional check if we trust the flow)
        // For security, we should probably check a flag in Cache/Session

        $referrer = null;
        if (!empty($data['ref'])) {
            $referrer = User::where('uuid', $data['ref'])->first();
        }

        $promo = null;
        if (!empty($data['promo_code'])) {
            $promo = PromoCode::where('code', $data['promo_code'])->first();
        }

        $user = DB::transaction(function () use ($data, $promo, $referrer) {
            // Generate unique referral code
            $referralCode = null;
            for ($i = 0; $i < 5; $i++) {
                $candidate = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(8));
                if (!User::where('referral_code', $candidate)->exists()) {
                    $referralCode = $candidate;
                    break;
                }
            }
            $referralCode = $referralCode ?: \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(8));

            $user = User::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'phone' => $data['phone'],
                'country_code' => $data['country_code'],
                'country' => $data['nationality'] ?? null,
                'gender' => $data['gender'] ?? null,
                'account_type' => $data['account_type'],
                'referred_by' => $referrer?->id,
                'referral_code' => $referralCode,
                'email_verified_at' => now(), // It was verified in Step 3
                'active' => true,
            ]);

            if ($promo && $promo->start_balance > 0) {
                $amount = (float)$promo->start_balance;
                Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'deposit',
                    'status' => 'confirmed',
                    'amount' => $amount,
                    'description' => 'Promo bonus',
                    'meta' => ['promo_code' => $promo->code],
                ]);
                $user->increment('balance', $amount);
                $user->increment('deposited', $amount);
                $promo->increment('used_count');
            }

            return $user;
        });

        Auth::login($user);

        try {
            $telegramBotService->sendUserRegistration($user, $request->ip(), 'api-form');
        } catch (\Exception $e) {}

        return response()->json([
            'success' => true,
            'redirect' => route('cabinet.dashboard')
        ]);
    }
}
