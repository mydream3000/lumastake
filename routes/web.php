<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\BlogPost;
use App\Models\Faq;
use App\Models\Tier;
use App\Models\User;
use App\Models\PromoCode;
use App\Models\Transaction;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TierController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProfitTiersController;

Route::view('/', 'public.home')->name('home');

Route::get('/about', [\App\Http\Controllers\AboutController::class, 'index'])->name('about');
Route::get('/tiers', [ProfitTiersController::class, 'index'])->name('profit-tiers');

Route::get('/blog', [BlogController::class, 'index'])->name('blog');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/faq', [FaqController::class, 'index'])->name('faq');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send')->middleware('throttle:3,10');
Route::view('/terms', 'public.terms')->name('terms');
Route::view('/privacy', 'public.privacy')->name('privacy');

// Auth: Login/Register/Logout
Route::view('/login', 'auth.login')->name('login');
Route::post('/login', [AuthController::class, 'attemptLogin'])->middleware('throttle:5,1')->name('login.attempt');
Route::post('/login/verify-2fa', [AuthController::class, 'verify2FACode'])->middleware('throttle:10,1')->name('login.verify-2fa');
Route::post('/login/resend-2fa', [AuthController::class, 'resend2FACode'])->middleware('throttle:3,1')->name('login.resend-2fa');

// Test login route
Route::get('/login/test', function (Request $request) {
    $userId = $request->get('user_id', 1);
    $user = User::find($userId);
    if ($user) {
        Auth::login($user);

        // Отправить уведомление о входе администратора (только для админов)
        if ($user->is_admin) {
            try {
                $telegramService = app(\App\Services\TelegramBotService::class);
                $telegramService->sendAdminLogin($user, $request->ip(), 'test');
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send admin login notification', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect('/dashboard');
    }
    return 'User not found';
});

Route::view('/register', 'auth.register')->name('register');
Route::post('/register', [RegisterController::class, 'submit'])->middleware('throttle:3,1')->name('register.submit');
Route::post('/register/verify-email', [RegisterController::class, 'verifyEmail'])->middleware('throttle:10,1')->name('register.verify-email');
Route::post('/register/resend-code', [RegisterController::class, 'resendCode'])->middleware('throttle:3,1')->name('register.resend-code');
Route::post('/register/save-account-type', [RegisterController::class, 'saveAccountType'])->name('register.save-account-type');

// New Multi-step API routes
Route::post('/register/step1-send-code', [RegisterController::class, 'step1SendCode'])->middleware('throttle:3,1');
Route::post('/register/step2-verify-code', [RegisterController::class, 'step2VerifyCode'])->middleware('throttle:10,1');
Route::post('/register/resend-code-api', [RegisterController::class, 'resendCodeApi'])->middleware('throttle:3,1');
Route::post('/register/finalize', [RegisterController::class, 'finalize']);

Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::post('/password/send-code', [PasswordResetController::class, 'sendCode'])->middleware('throttle:3,1')->name('password.send-code');
Route::post('/password/verify-code', [PasswordResetController::class, 'verifyCode'])->middleware('throttle:10,1')->name('password.verify-code');
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword'])->middleware('throttle:5,1')->name('password.reset');
Route::post('/password/resend-code', [PasswordResetController::class, 'resendCode'])->middleware('throttle:3,1')->name('password.resend-code');

// Socialite Routes
Route::get('auth/{provider}', [SocialiteController::class, 'handleProvider'])->name('social.login');
Route::get('auth/{provider}/callback', [SocialiteController::class, 'handleProvider'])->name('social.callback');


// SEO public endpoints
Route::get('/robots.txt', [\App\Http\Controllers\SeoPublicController::class, 'robots']);
Route::get('/sitemap.xml', [\App\Http\Controllers\SeoPublicController::class, 'sitemap']);

require __DIR__.'/cabinet.php';
require __DIR__.'/admin.php';

