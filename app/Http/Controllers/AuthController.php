<?php

namespace App\Http\Controllers;

use App\Mail\TemplatedMail;
use App\Models\ToastMessage;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AuthController extends BaseController
{
    const MAX_LOGIN_ATTEMPTS = 3;

    public function attemptLogin(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email|max:255',
                'password' => 'required|string',
                'remember' => 'nullable',
            ]);

            $remember = $request->boolean('remember');

            // Find user by email
            $user = \App\Models\User::where('email', $credentials['email'])->first();

            // Check if user is blocked
            if ($user && $user->blocked) {
                $errorMessage = 'Your account has been blocked, please contact the site administration.';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                    ]);
                }
                return back()
                    ->withErrors(['auth' => $errorMessage])
                    ->withInput($request->only('email', 'remember'));
            }

            // Check credentials
            if (!$user || !password_verify($credentials['password'], $user->password)) {
                // Failed login attempt
                if ($user) {
                    $attempts = $user->failed_login_attempts + 1;

                    if ($attempts >= self::MAX_LOGIN_ATTEMPTS) {
                        // Block the user account
                        $user->update([
                            'blocked' => true,
                            'failed_login_attempts' => $attempts,
                            'account_locked_at' => now(),
                        ]);

                        // Send account locked email notification (используем шаблон из БД)
                        try {
                            Mail::to($user->email)->queue(new TemplatedMail(
                                'account_locked',
                                [
                                    'userName' => $user->name,
                                    'lockedAt' => now()->format('d M Y, H:i'),
                                ],
                                $user->id
                            ));
                        } catch (\Exception $e) {
                            Log::error('Failed to send account locked email: ' . $e->getMessage());
                        }

                        $errorMessage = 'Your account has been locked due to multiple failed login attempts. Please contact support.';
                        if ($request->ajax() || $request->wantsJson()) {
                            return response()->json([
                                'success' => false,
                                'message' => $errorMessage,
                            ]);
                        }
                        return back()
                            ->withErrors(['auth' => $errorMessage])
                            ->withInput($request->only('email', 'remember'));
                    } else {
                        // Increment failed attempts counter
                        $user->increment('failed_login_attempts');

                        $remainingAttempts = self::MAX_LOGIN_ATTEMPTS - $attempts;
                        $errorMessage = "Invalid credentials. You have {$remainingAttempts} attempt(s) remaining before your account is locked.";
                        if ($request->ajax() || $request->wantsJson()) {
                            return response()->json([
                                'success' => false,
                                'message' => $errorMessage,
                            ]);
                        }
                        return back()
                            ->withErrors(['auth' => $errorMessage])
                            ->withInput($request->only('email', 'remember'));
                    }
                }

                $errorMessage = 'Invalid credentials.';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                    ]);
                }
                return back()
                    ->withErrors(['auth' => $errorMessage])
                    ->withInput($request->only('email', 'remember'));
            }

            // Credentials are correct
            // Check if user is admin - skip 2FA for admins
            if ($user->is_admin) {
                // Reset failed login attempts
                $user->update([
                    'failed_login_attempts' => 0,
                    'account_locked_at' => null,
                ]);

                Auth::login($user, $remember);
                $request->session()->regenerate();

                // Отправить уведомление о входе администратора
                try {
                    if (class_exists(\App\Services\TelegramBotService::class)) {
                        $telegramService = app(\App\Services\TelegramBotService::class);
                        $telegramService->sendAdminLogin($user, $request->ip(), 'standard');
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send admin login notification: ' . $e->getMessage());
                }

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'redirect' => route('cabinet.dashboard'),
                    ]);
                }
                return redirect()->intended(route('cabinet.dashboard'));
            }

            // Check if 2FA was verified within last 12 hours - skip 2FA if yes
            if ($user->login_2fa_last_verified_at && $user->login_2fa_last_verified_at->gt(now()->subHours(12))) {
                // Reset failed login attempts
                $user->update([
                    'failed_login_attempts' => 0,
                    'account_locked_at' => null,
                ]);

                Auth::login($user, $remember);
                $request->session()->regenerate();

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'redirect' => route('cabinet.dashboard'),
                    ]);
                }
                return redirect()->intended(route('cabinet.dashboard'));
            }

            // For non-admin users: Generate and send 2FA code
            $verificationCode = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);

            $user->update([
                'login_2fa_code' => $verificationCode,
                'login_2fa_code_expires_at' => now()->addMinutes(60),
                'login_2fa_verified' => false,
                'failed_login_attempts' => 0,
                'account_locked_at' => null,
            ]);

            // Send verification email
            $emailSent = false;
            try {
                Mail::mailer('failover')->to($user->email)->send(new TemplatedMail(
                    'password_code_remainder',
                    [
                        'code' => $verificationCode,
                        'userName' => $user->name ?: $user->email,
                    ],
                    $user->id
                ));
                $emailSent = true;
            } catch (\Exception $e) {
                Log::error('Failed to send 2FA login code: ' . $e->getMessage());
            }

            // Store user ID in session but don't log in yet
            session([
                'pending_2fa_user_id' => $user->id,
                '2fa_email_sent' => $emailSent,
                '2fa_email' => $user->email,
                '2fa_remember' => $remember,
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'two_fa_required' => true,
                    'message' => '2FA code sent to your email.',
                    'email' => $user->email,
                ]);
            }

            return redirect()->route('login')->with([
                'show_2fa_modal' => true,
                '2fa_email_sent' => $emailSent,
                '2fa_email' => $user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => config('app.debug') ? $e->getMessage() : 'An error occurred during login.',
                ], 500);
            }

            return back()->withErrors(['auth' => 'An unexpected error occurred.'])->withInput();
        }
    }

    public function verify2FACode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $userId = session('pending_2fa_user_id');

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => '2FA session expired. Please login again.',
            ], 400);
        }

        $user = \App\Models\User::find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Check if code matches and not expired
        if ($user->login_2fa_code !== $request->code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code.',
            ], 400);
        }

        if ($user->login_2fa_code_expires_at < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Verification code has expired.',
            ], 400);
        }

        // Mark as verified and clear code
        $user->login_2fa_verified = true;
        $user->login_2fa_code = null;
        $user->login_2fa_code_expires_at = null;
        $user->login_2fa_last_verified_at = now(); // Save timestamp for 24-hour session
        $user->save();

        // Get remember flag from session
        $remember = session('2fa_remember', false);

        // Clear session and log in user
        session()->forget(['pending_2fa_user_id', '2fa_email_sent', '2fa_email', '2fa_remember']);
        Auth::login($user, $remember);

        // Отправить уведомление о входе администратора (только для админов)
        if ($user->is_admin) {
            try {
                $telegramService = app(\App\Services\TelegramBotService::class);
                $telegramService->sendAdminLogin($user, $request->ip(), '2fa');
            } catch (\Exception $e) {
                Log::error('Failed to send admin login notification', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful!',
            'redirect' => route('cabinet.dashboard'),
        ]);
    }

    public function resend2FACode(Request $request)
    {
        $userId = session('pending_2fa_user_id');

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => '2FA session expired.',
            ], 400);
        }

        $user = \App\Models\User::find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Generate new code
        $verificationCode = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->login_2fa_code = $verificationCode;
        $user->login_2fa_code_expires_at = now()->addMinutes(60);
        $user->save();

        // Send verification email
        try {
            Log::info('Attempting to resend 2FA login code', [
                'email' => $user->email,
                'code' => $verificationCode,
            ]);

            Mail::mailer('failover')->to($user->email)->send(new TemplatedMail(
                'password_code_remainder',
                [
                    'code' => $verificationCode,
                    'userName' => $user->name ?: $user->email,
                ],
                $user->id
            ));

            Log::info('2FA login code resent successfully', [
                'email' => $user->email,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Verification code resent successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to resend 2FA login code', [
                'email' => $user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send email. Please try again later.',
            ], 200);
        }
    }
}
