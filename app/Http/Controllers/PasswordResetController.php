<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Mail\TemplatedMail;

class PasswordResetController extends BaseController
{
    /**
     * Send password reset code to email
     */
    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email:rfc,dns|max:255',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Don't reveal if email exists or not for security
            return response()->json([
                'success' => true,
                'message' => 'If an account exists with this email, you will receive a password reset code.',
                'code_sent' => false,
            ]);
        }

        // Blocked users cannot reset password
        if ($user->blocked) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been blocked, please contact the site administration.',
                'code_sent' => false,
            ], 200);
        }

        // Determine storage strategy
        $useDbColumns = Schema::hasColumn('users', 'password_reset_code') && Schema::hasColumn('users', 'password_reset_code_expires_at');

        // Generate 6-digit code
        $resetCode = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(60);

        if ($useDbColumns) {
            // Store code and expiration in database (guarded by try/catch)
            try {
                $user->password_reset_code = $resetCode;
                $user->password_reset_code_expires_at = $expiresAt;
                $user->save();
            } catch (\Throwable $e) {
                Log::error('Failed to persist password reset code', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Password reset is temporarily unavailable. Please try again later.',
                    'code_sent' => false,
                ], 200);
            }
        } else {
            // Fallback: session-based storage to keep UX working even without migration or when cache store is non-persistent
            session([
                'password_reset_fallback_code' => $resetCode,
                'password_reset_fallback_expires_at' => $expiresAt->toIso8601String(),
            ]);
            Log::warning('Using session fallback for password reset code (migration missing or non-persistent cache)', [
                'email' => $user->email,
            ]);
        }

        Log::info('Password reset code generated', [
            'user_id' => $user->id,
            'email' => $user->email,
            'code' => $resetCode,
            'storage' => $useDbColumns ? 'db' : 'session',
        ]);

        // Send email with code
        try {
            // Use failover mailer (smtp -> log) to avoid hard failures on production
            Mail::mailer('failover')->to($user->email)->send(new TemplatedMail(
                'password_reset',
                ['code' => $resetCode, 'userName' => $user->name],
                $user->id
            ));

            Log::info('Password reset email sent', [
                'email' => $user->email,
                'user_id' => $user->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send password reset email', [
                'email' => $user->email,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            // Do not return 500 to frontend; keep UX responsive with a 200 status
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email. Please try again later.',
                'code_sent' => false,
            ], 200);
        }

        // Store email in session for next step
        session(['password_reset_email' => $user->email]);

        return response()->json([
            'success' => true,
            'message' => 'Password reset code sent to your email.',
            'code_sent' => true,
        ]);
    }

    /**
     * Verify reset code
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $email = session('password_reset_email');

        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please start over.',
            ], 400);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Blocked users cannot verify reset codes
        if ($user->blocked) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been blocked, please contact the site administration.',
            ], 200);
        }

        $useDbColumns = Schema::hasColumn('users', 'password_reset_code') && Schema::hasColumn('users', 'password_reset_code_expires_at');

        if ($useDbColumns) {
            Log::info('Password reset code verification attempt (db)', [
                'user_id' => $user->id,
                'stored_code' => $user->password_reset_code,
                'input_code' => $request->code,
                'expires_at' => $user->password_reset_code_expires_at,
            ]);

            // Check if code matches
            if ($user->password_reset_code !== $request->code) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid reset code.',
                ], 400);
            }

            // Check if code expired
            if ($user->password_reset_code_expires_at < now()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reset code has expired.',
                ], 400);
            }
        } else {
            // Session-based fallback verification
            $storedCode = session('password_reset_fallback_code');
            $expiresAtIso = session('password_reset_fallback_expires_at');

            Log::info('Password reset code verification attempt (session)', [
                'email' => $user->email,
                'has_code' => (bool) $storedCode,
                'expires_at' => $expiresAtIso,
            ]);

            if (!$storedCode || !$expiresAtIso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expired. Please request a new code.',
                ], 400);
            }

            if ($storedCode !== $request->code) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid reset code.',
                ], 400);
            }

            $expiresAt = \Carbon\Carbon::parse($expiresAtIso);
            if ($expiresAt->lt(now())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reset code has expired.',
                ], 400);
            }
        }

        // Store verified flag in session
        session(['password_reset_verified' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Code verified successfully.',
        ]);
    }

    /**
     * Reset password with new password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $email = session('password_reset_email');
        $verified = session('password_reset_verified');

        if (!$email || !$verified) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Please verify code first.',
            ], 401);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Blocked users cannot reset password
        if ($user->blocked) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been blocked, please contact the site administration.',
            ], 200);
        }

        // Update password
        $user->password = $request->password; // Will be hashed by cast

        // Best-effort cleanup if columns exist
        if (Schema::hasColumn('users', 'password_reset_code')) {
            $user->password_reset_code = null;
        }
        if (Schema::hasColumn('users', 'password_reset_code_expires_at')) {
            $user->password_reset_code_expires_at = null;
        }
        $user->save();

        Log::info('Password reset successful', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        // Auth user after password reset
        \Illuminate\Support\Facades\Auth::login($user, true);

        // Clear session and any fallback payload
        session()->forget(['password_reset_email', 'password_reset_verified', 'password_reset_fallback_code', 'password_reset_fallback_expires_at']);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully!',
            'redirect' => route('cabinet.dashboard'),
        ]);
    }

    /**
     * Resend reset code
     */
    public function resendCode(Request $request)
    {
        $email = session('password_reset_email');

        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please start over.',
            ], 400);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Blocked users cannot receive resent codes
        if ($user->blocked) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been blocked, please contact the site administration.',
            ], 200);
        }

        $useDbColumns = Schema::hasColumn('users', 'password_reset_code') && Schema::hasColumn('users', 'password_reset_code_expires_at');

        // Generate new code
        $resetCode = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(60);

        if ($useDbColumns) {
            $user->password_reset_code = $resetCode;
            $user->password_reset_code_expires_at = $expiresAt;
            $user->save();
        } else {
            // Session-based fallback storage for resend (when migration missing or cache is non-persistent)
            session([
                'password_reset_fallback_code' => $resetCode,
                'password_reset_fallback_expires_at' => $expiresAt->toIso8601String(),
            ]);
            Log::warning('Using session fallback for resend password reset code (migration missing or non-persistent cache)', [
                'email' => $user->email,
            ]);
        }

        // Send email
        try {
            // Use failover mailer (smtp -> log) to avoid hard failures on production
            Mail::mailer('failover')->to($user->email)->send(new TemplatedMail(
                'password_reset',
                ['code' => $resetCode, 'userName' => $user->name],
                $user->id
            ));

            Log::info('Password reset code resent', [
                'email' => $user->email,
                'user_id' => $user->id,
                'storage' => $useDbColumns ? 'db' : 'cache',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reset code resent successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to resend password reset email', [
                'email' => $user->email,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            // Respond with 200 to avoid frontend 500, but indicate failure
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email. Please try again later.',
            ], 200);
        }
    }
}
