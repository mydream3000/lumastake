<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cabinet\ProfileUpdateRequest;
use App\Mail\EmailVerificationCode;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ProfileController extends Controller
{
    public function show()
    {
        return view('cabinet.profile.show', ['user' => auth()->user()]);
    }

    /**
     * Отправка кода подтверждения e-mail текущему пользователю (для первичной верификации или повторной отправки)
     */
    public function sendEmailVerification(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();

        // Если уже верифицирован — ничего не делаем
        if ($user->email_verified_at) {
            \App\Models\ToastMessage::create([
                'user_id' => $user->id,
                'message' => 'Your email is already verified.',
                'type' => 'info',
            ]);
            return redirect()->back();
        }

        // Генерируем/обновляем код на 6 цифр, срок — 15 минут
        $code = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->email_verification_code = $code;
        $user->email_verification_code_expires_at = now()->addMinutes(15);
        $user->save();

        try {
            // Отправляем через систему шаблонов, редактируемых в админке
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\TemplatedMail(
                'email_verification',
                [
                    'code' => $code,
                    'userName' => $user->name ?? $user->email,
                ],
                $user->id,
                'email_verification'
            ));

            \App\Models\ToastMessage::create([
                'user_id' => $user->id,
                'message' => 'Verification code has been sent to your email.',
                'type' => 'success',
            ]);
        } catch (\Throwable $e) {
            \Log::error('Failed to send email verification code', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            \App\Models\ToastMessage::create([
                'user_id' => $user->id,
                'message' => 'Failed to send verification email. Please try again later.',
                'type' => 'error',
            ]);
        }

        return redirect()->back();
    }

    public function edit()
    {
        return view('cabinet.profile.edit', [
            'user' => auth()->user(),
            'countries' => Country::orderBy('name')->get(),
        ]);
    }

    public function update(ProfileUpdateRequest $request)
    {
        $user = auth()->user();

        try {
            // DEBUG: Log all request data
            \Log::info('Profile update attempt', [
                'user_id' => $user->id,
                'has_file' => $request->hasFile('avatar'),
                'files' => $request->allFiles(),
                'all_data' => $request->except('avatar'),
            ]);

            $data = $request->except(['avatar', 'email']);

            // Normalize phone and country_code if provided
            if (array_key_exists('phone', $data) || array_key_exists('country_code', $data)) {
                $rawPhone = (string)($data['phone'] ?? '');
                $rawCountryCode = (string)($data['country_code'] ?? '');

                // Ensure country_code like +<digits>
                $normalizedCountryCode = $rawCountryCode;
                if ($normalizedCountryCode !== '' && $normalizedCountryCode[0] !== '+') {
                    if (preg_match('/^\d{1,4}$/', $normalizedCountryCode)) {
                        $normalizedCountryCode = '+' . $normalizedCountryCode;
                    }
                }

                // Digits-only national phone
                $digitsPhone = preg_replace('/\D+/', '', $rawPhone);
                $dialDigits = preg_replace('/\D+/', '', $normalizedCountryCode);
                if ($dialDigits && str_starts_with($digitsPhone, $dialDigits)) {
                    $digitsPhone = substr($digitsPhone, strlen($dialDigits));
                }

                $data['phone'] = $digitsPhone !== '' ? $digitsPhone : null;
                $data['country_code'] = $normalizedCountryCode !== '' ? $normalizedCountryCode : null;
            }

            // Проверяем, изменился ли email
            if ($request->has('email') && $request->email !== $user->email) {
                // Сохраняем новый email во временное поле
                $user->new_email = $request->email;

                // Генерируем код подтверждения
                $code = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $user->email_change_code = $code;
                $user->email_change_code_expires_at = now()->addMinutes(10);
                $user->save();

                // Отправляем код на НОВУЮ почту
                Mail::mailer('failover')->to($user->new_email)->send(new EmailVerificationCode(
                    $code,
                    $user->name,
                    'email_change'
                ));

                \App\Models\ToastMessage::create([
                    'user_id' => $user->id,
                    'message' => 'Verification code sent to your new email. Please check your inbox.',
                    'type' => 'info',
                ]);

                return redirect()->back()->with('email_verification_required', true);
            }

            $user->update($data);

            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');

                // DEBUG: Log file details
                \Log::info('Avatar file detected', [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'is_valid' => $file->isValid(),
                    'error' => $file->getError(),
                ]);

                // Delete old avatar if exists
                if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
                    \Log::info('Deleting old avatar', ['path' => $user->avatar]);
                    \Storage::disk('public')->delete($user->avatar);
                }

                // Store new avatar
                $path = $file->store('avatars', 'public');
                \Log::info('Avatar stored', [
                    'path' => $path,
                    'full_path' => \Storage::disk('public')->path($path),
                    'exists' => \Storage::disk('public')->exists($path),
                ]);

                $user->update(['avatar' => $path]);

                \App\Models\ToastMessage::create([
                    'user_id' => $user->id,
                    'message' => 'Profile and avatar updated successfully',
                    'type' => 'success',
                ]);
            } else {
                \Log::warning('No avatar file in request');

                \App\Models\ToastMessage::create([
                    'user_id' => $user->id,
                    'message' => 'Profile updated successfully',
                    'type' => 'success',
                ]);
            }

            return redirect()->route('cabinet.profile.show');
        } catch (\Exception $e) {
            \Log::error('Profile update failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            \App\Models\ToastMessage::create([
                'user_id' => $user->id,
                'message' => 'Failed to update profile. Please try again.',
                'type' => 'error',
            ]);

            return redirect()->back()->withInput();
        }
    }

    /**
     * Подтверждение смены email через OTP код
     */
    public function verifyEmailChange(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $user = auth()->user();

        // Проверяем наличие кода
        if (!$user->email_change_code) {
            return response()->json([
                'success' => false,
                'message' => 'No email change request found.',
            ], 400);
        }

        // Проверяем истёк ли код
        if ($user->email_change_code_expires_at < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Verification code has expired. Please request a new one.',
            ], 400);
        }

        // Проверяем совпадение кода
        if ($user->email_change_code !== $request->code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code.',
            ], 400);
        }

        // Применяем изменение email
        $user->email = $user->new_email;
        $user->new_email = null;
        $user->email_change_code = null;
        $user->email_change_code_expires_at = null;
        $user->save();

        \App\Models\ToastMessage::create([
            'user_id' => $user->id,
            'message' => 'Email updated successfully!',
            'type' => 'success',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Email updated successfully!',
        ]);
    }

    /**
     * Повторная отправка кода для смены email
     */
    public function resendEmailChangeCode(Request $request)
    {
        $user = auth()->user();

        if (!$user->new_email) {
            return response()->json([
                'success' => false,
                'message' => 'No email change request found.',
            ], 400);
        }

        // Генерируем новый код
        $code = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->email_change_code = $code;
        $user->email_change_code_expires_at = now()->addMinutes(10);
        $user->save();

        // Отправляем код на НОВУЮ почту
        Mail::mailer('failover')->to($user->new_email)->send(new EmailVerificationCode(
            $code,
            $user->name,
            'email_change'
        ));

        return response()->json([
            'success' => true,
            'message' => 'New verification code sent to your new email.',
        ]);
    }

    /**
     * Смена пароля пользователя
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Current password is required.',
            'new_password.required' => 'New password is required.',
            'new_password.min' => 'New password must be at least 8 characters.',
            'new_password.confirmed' => 'Passwords do not match.',
        ]);

        $user = auth()->user();

        // Проверяем текущий пароль
        if (!password_verify($request->current_password, $user->password)) {
            \App\Models\ToastMessage::create([
                'user_id' => $user->id,
                'message' => 'Current password is incorrect.',
                'type' => 'error',
            ]);

            return redirect()->back()->withErrors([
                'current_password' => 'Current password is incorrect.'
            ]);
        }

        // Проверяем, что новый пароль отличается от текущего
        if (password_verify($request->new_password, $user->password)) {
            \App\Models\ToastMessage::create([
                'user_id' => $user->id,
                'message' => 'New password must be different from the current password.',
                'type' => 'error',
            ]);

            return redirect()->back()->withErrors([
                'new_password' => 'New password must be different from the current password.'
            ]);
        }

        // Обновляем пароль
        $user->update([
            'password' => $request->new_password, // будет хешировано автоматически через cast
        ]);

        \App\Models\ToastMessage::create([
            'user_id' => $user->id,
            'message' => 'Password changed successfully!',
            'type' => 'success',
        ]);

        return redirect()->route('cabinet.profile.show');
    }
}
