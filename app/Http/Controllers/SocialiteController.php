<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TelegramBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Handle OAuth provider authentication (redirect or callback)
     *
     * @param $provider
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Illuminate\Http\RedirectResponse
     */
    public function handleProvider($provider)
    {
        // Check if this is a callback (has 'code' or 'error' parameter)
        if (request()->has('code') || request()->has('error')) {
            return $this->handleCallback($provider);
        }

        // Otherwise, redirect to provider
        // For local development, disable SSL verification
        $driver = Socialite::driver($provider);

        if ($provider === 'google') {
            // Ensure we request email explicitly
            $driver = $driver->scopes(['openid', 'profile', 'email']);
        }

        if (config('app.env') === 'local') {
            $driver->setHttpClient(new \GuzzleHttp\Client(['verify' => false]));
        }

        // Capture referral uuid in session for guests (if present)
        if (!auth()->check()) {
            $ref = request()->query('ref');
            if (!empty($ref)) {
                session(['ref' => $ref]); // Используем ключ 'ref' для консистентности
            }
        }

        // Ensure session is saved before redirect (important for account_type persistence)
        session()->save();

        Log::info('Redirecting to OAuth provider', [
            'provider' => $provider,
            'has_pending_account_type' => session()->has('pending_account_type'),
            'account_type' => session('pending_account_type'),
            'session_id' => session()->getId(),
        ]);

        return $driver->redirect();
    }

    /**
     * Handle the callback from OAuth provider
     *
     * @param $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    private function handleCallback($provider)
    {
        try {
            Log::info('OAuth callback received', [
                'provider' => $provider,
                'has_code' => request()->has('code'),
                'has_error' => request()->has('error'),
                'session_id' => session()->getId(),
                'has_pending_account_type' => session()->has('pending_account_type'),
                'pending_account_type' => session('pending_account_type'),
            ]);

            // Build Socialite driver
            $driver = Socialite::driver($provider);

            if ($provider === 'google') {
                $driver = $driver->scopes(['openid', 'profile', 'email']);
            }

            // For local development, disable SSL verification
            if (config('app.env') === 'local') {
                $driver->setHttpClient(new \GuzzleHttp\Client(['verify' => false]));
            }

            // Use stateless in non-local envs to avoid state mismatch behind proxies/CDNs
            if (!app()->environment('local')) {
                $driver = $driver->stateless();
            }

            $socialUser = $driver->user();

            Log::info('Social user retrieved', [
                'provider' => $provider,
                'email' => $socialUser->getEmail(),
                'name' => $socialUser->getName(),
                'id' => $socialUser->getId(),
            ]);
        } catch (\Exception $e) {
            Log::error('OAuth error', [
                'provider' => $provider,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('login')->withErrors(['auth' => 'An error occurred while authenticating with '.ucfirst($provider).'.']);
        }

        // Ensure email is present (Google may not return it if the user denied scope)
        $email = $socialUser->getEmail();
        if (!$email) {
            Log::warning('Social login without email', [
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
            ]);
            return redirect()->route('login')->withErrors(['auth' => 'We could not retrieve your email from '.ucfirst($provider).'. Please allow email access or use email/password login.']);
        }

        // Try to find by provider id first
        $user = User::where('google_id', $socialUser->getId())->first();

        if (!$user) {
            // Fallback: find by email
            $user = User::where('email', $email)->first();
        }

        if ($user) {
            // Blocked users cannot login via social auth
            if ($user->blocked) {
                Log::warning('Blocked user attempted social login', [
                    'provider' => $provider,
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
                return redirect()->route('login')->withErrors(['auth' => 'Your account has been blocked, please contact the site administration.']);
            }

            // Link provider id if missing
            if (!$user->google_id) {
                $user->google_id = $socialUser->getId();
            }
            // Verify email if not verified
            if (!$user->email_verified_at) {
                $user->email_verified_at = now();
            }
            $user->save();

            Auth::login($user, true);

            Log::info('Social login existing user', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            // Отправить уведомление о входе администратора (только для админов)
            if ($user->is_admin) {
                try {
                    $telegramService = app(TelegramBotService::class);
                    $telegramService->sendAdminLogin($user, request()->ip(), 'google');
                } catch (\Exception $e) {
                    Log::error('Failed to send admin login notification', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        } else {
            // Create a unique referral code with a few attempts
            $referral = null;
            for ($i = 0; $i < 5; $i++) {
                $candidate = Str::upper(Str::random(8));
                if (!User::where('referral_code', $candidate)->exists()) {
                    $referral = $candidate;
                    break;
                }
            }
            $referral = $referral ?: Str::upper(Str::random(8));

            try {
                // Resolve referrer for NEW user only from session (support uuid or referral_code)
                $referredById = null;

                $refUserId = session('referrer_user_id');
                $rawRef = session('ref'); // may be uuid or referral_code

                if ($refUserId) {
                    $refUser = User::find($refUserId);
                    if ($refUser && $refUser->email !== $email) { // simple self-ref guard by email
                        $referredById = $refUser->id;
                        Log::info('Applying referral on Google signup (by id)', [
                            'referrer_id' => $referredById,
                        ]);
                    } else {
                        Log::warning('Referral skipped on Google signup (invalid or self by id)', [
                            'referrer_id' => $refUserId,
                        ]);
                    }
                } elseif (!empty($rawRef)) {
                    $refTrim = trim($rawRef);
                    $refUpper = Str::upper($refTrim);
                    $refUser = Str::isUuid($refTrim)
                        ? User::where('uuid', $refTrim)->first()
                        : User::where('referral_code', $refUpper)->first();
                    if ($refUser && $refUser->email !== $email) {
                        $referredById = $refUser->id;
                        Log::info('Applying referral on Google signup (by ref)', [
                            'referrer_id' => $referredById,
                            'ref' => $rawRef,
                        ]);
                    } else {
                        Log::warning('Referral skipped on Google signup (invalid or self by ref)', [
                            'ref' => $rawRef,
                        ]);
                    }
                }

                // Get account type from session (saved before OAuth redirect)
                $accountType = session('pending_account_type', 'normal');

                Log::info('Retrieved account type from session for Google OAuth', [
                    'account_type' => $accountType,
                    'session_id' => session()->getId(),
                    'has_pending_account_type' => session()->has('pending_account_type'),
                ]);

                // clear session keys regardless of outcome
                session()->forget(['referrer_user_id', 'ref', 'referrer_uuid', 'pending_account_type']);

                $user = User::create([
                    'name' => $socialUser->getName() ?: explode('@', $email)[0],
                    'email' => $email,
                    'google_id' => $socialUser->getId(),
                    'password' => Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                    'referral_code' => $referral,
                    'referred_by' => $referredById,
                    'account_type' => $accountType,
                ]);

                Log::info('Social user created via Google OAuth', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'account_type' => $user->account_type,
                    'account_type_from_session' => $accountType,
                ]);

                // Send Telegram notification about new Google OAuth registration
                try {
                    $telegramService = app(TelegramBotService::class);
                    $ipAddress = request()->ip();
                    $telegramService->sendUserRegistration($user, $ipAddress, 'google');
                } catch (\Exception $e) {
                    Log::error('Failed to send Telegram notification for Google OAuth registration', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ]);
                    // Don't fail registration if Telegram notification fails
                }

                Auth::login($user, true);
            } catch (\Exception $e) {
                Log::error('Failed to create social user', [
                    'provider' => $provider,
                    'email' => $email,
                    'error' => $e->getMessage(),
                ]);
                return redirect()->route('login')->withErrors(['auth' => 'We could not complete your sign-in. Please try again.']);
            }
        }

        return redirect()->intended(route('cabinet.dashboard'));
    }
}
