<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\BotSettingsController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PoolController;
use App\Http\Controllers\Admin\ReferralLevelController;
use App\Http\Controllers\Admin\SocialLinksController;
use App\Http\Controllers\Admin\TierController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Return from impersonation - OUTSIDE admin middleware group
Route::post('/admin/return-to-admin', [UserController::class, 'returnToAdmin'])
    ->middleware('auth')
    ->name('admin.return-to-admin');

// Admin routes. Protected by 'auth' and custom 'admin' middleware alias.
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Users Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/export', [UserController::class, 'export'])->name('export');
        // Bulk actions
        Route::post('/bulk/adjust-balance', [UserController::class, 'bulkAdjustBalance'])->name('bulk.adjust-balance');
        Route::post('/bulk/block', [UserController::class, 'bulkBlock'])->name('bulk.block');
        Route::post('/bulk/unblock', [UserController::class, 'bulkUnblock'])->name('bulk.unblock');
        Route::post('/bulk/block-withdrawal', [UserController::class, 'bulkBlockWithdrawal'])->name('bulk.block-withdrawal');
        Route::post('/bulk/unblock-withdrawal', [UserController::class, 'bulkUnblockWithdrawal'])->name('bulk.unblock-withdrawal');
        Route::post('/bulk/send-email', [UserController::class, 'bulkSendEmail'])->name('bulk.send-email');
        Route::post('/bulk/delete', [UserController::class, 'bulkDelete'])->name('bulk.delete');

        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{user}/toggle-favorite', [UserController::class, 'toggleFavorite'])->name('toggle-favorite');
        Route::post('/{user}/login-as', [UserController::class, 'loginAs'])->name('login-as');
        Route::post('/{user}/adjust-balance', [UserController::class, 'adjustBalance'])->name('adjust-balance');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    // Payments Management
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/export', [PaymentController::class, 'export'])->name('export');
        Route::get('/stats', [PaymentController::class, 'stats'])->name('stats');
        Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
        Route::post('/{payment}/approve', [PaymentController::class, 'approve'])->name('approve');
        Route::post('/{payment}/reject', [PaymentController::class, 'reject'])->name('reject');
        Route::post('/{payment}/complete', [PaymentController::class, 'complete'])->name('complete');
    });

    // Tiers Management
    Route::prefix('tiers')->name('tiers.')->group(function () {
        Route::get('/', [TierController::class, 'index'])->name('index');
        Route::get('/{tier}/edit', [TierController::class, 'edit'])->name('edit');
        Route::put('/{tier}', [TierController::class, 'update'])->name('update');
        Route::post('/{tier}/percentages', [TierController::class, 'updatePercentages'])->name('update-percentages');
    });

    // Investment Pools Management
    Route::prefix('pools')->name('pools.')->group(function () {
        Route::get('/', [PoolController::class, 'index'])->name('index');
        Route::post('/', [PoolController::class, 'store'])->name('store');
        Route::put('/{pool}', [PoolController::class, 'update'])->name('update');
        Route::post('/{pool}/toggle-active', [PoolController::class, 'toggleActive'])->name('toggle-active');
        Route::delete('/{pool}', [PoolController::class, 'destroy'])->name('destroy');
        Route::get('/percentages', [PoolController::class, 'editPercentages'])->name('percentages');
        Route::post('/percentages', [PoolController::class, 'updatePercentages'])->name('update-percentages');
    });

    // Referral Levels Management
    Route::prefix('referral-levels')->name('referral-levels.')->group(function () {
        Route::get('/', [ReferralLevelController::class, 'index'])->name('index');
        Route::put('/{referralLevel}', [ReferralLevelController::class, 'update'])->name('update');
    });

    // FAQ Management
    Route::resource('faqs', FaqController::class);

    // Blog Management
    Route::resource('blog', BlogController::class)->except(['show']);

    // Telegram Bot Settings
    Route::prefix('bot-settings')->name('bot-settings.')->group(function () {
        Route::get('/', [BotSettingsController::class, 'index'])->name('index');
        Route::put('/', [BotSettingsController::class, 'update'])->name('update');
        Route::post('/test', [BotSettingsController::class, 'test'])->name('test');
    });

    // Email Templates Management
    Route::prefix('email-templates')->name('email-templates.')->group(function () {
        Route::get('/', [EmailTemplateController::class, 'index'])->name('index');
        Route::get('/create', [EmailTemplateController::class, 'create'])->name('create');
        Route::post('/', [EmailTemplateController::class, 'store'])->name('store');
        Route::get('/settings', [EmailTemplateController::class, 'settings'])->name('settings');
        Route::put('/settings', [EmailTemplateController::class, 'updateSettings'])->name('update-settings');
        Route::get('/{emailTemplate}/edit', [EmailTemplateController::class, 'edit'])->name('edit');
        Route::put('/{emailTemplate}', [EmailTemplateController::class, 'update'])->name('update');
        Route::post('/{emailTemplate}/send-test', [EmailTemplateController::class, 'sendTest'])->name('send-test');
    });

    // Support Team Management (Emails & Telegram Bots)
    Route::prefix('support-team')->name('support-team.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SupportTeamController::class, 'index'])->name('index');

        // Support Emails
        Route::post('/emails', [\App\Http\Controllers\Admin\SupportTeamController::class, 'storeEmail'])->name('emails.store');
        Route::put('/emails/{supportEmail}', [\App\Http\Controllers\Admin\SupportTeamController::class, 'updateEmail'])->name('emails.update');
        Route::delete('/emails/{supportEmail}', [\App\Http\Controllers\Admin\SupportTeamController::class, 'destroyEmail'])->name('emails.destroy');
        Route::post('/emails/{supportEmail}/toggle-status', [\App\Http\Controllers\Admin\SupportTeamController::class, 'toggleEmailStatus'])->name('emails.toggle-status');

        // Telegram Bots
        Route::post('/bots', [\App\Http\Controllers\Admin\SupportTeamController::class, 'storeBot'])->name('bots.store');
        Route::put('/bots/{telegramBot}', [\App\Http\Controllers\Admin\SupportTeamController::class, 'updateBot'])->name('bots.update');
        Route::delete('/bots/{telegramBot}', [\App\Http\Controllers\Admin\SupportTeamController::class, 'destroyBot'])->name('bots.destroy');
        Route::post('/bots/{telegramBot}/toggle-status', [\App\Http\Controllers\Admin\SupportTeamController::class, 'toggleBotStatus'])->name('bots.toggle-status');
        Route::post('/bots/{telegramBot}/test', [\App\Http\Controllers\Admin\SupportTeamController::class, 'testBot'])->name('bots.test');

        // Contact Info
        Route::post('/contact-info', [\App\Http\Controllers\Admin\SupportTeamController::class, 'updateContactInfo'])->name('contact-info.update');
    });

    // Social Links Management
    Route::prefix('social-links')->name('social-links.')->group(function () {
        Route::get('/', [SocialLinksController::class, 'index'])->name('index');
        Route::put('/{socialLink}', [SocialLinksController::class, 'update'])->name('update');
        Route::post('/{socialLink}/toggle-status', [SocialLinksController::class, 'toggleStatus'])->name('toggle-status');
    });

    // SEO Management
    Route::prefix('seo')->name('seo.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SeoController::class, 'index'])->name('index');
        Route::post('/page/{pageKey}', [\App\Http\Controllers\Admin\SeoController::class, 'updatePage'])->name('update-page');
        Route::post('/robots', [\App\Http\Controllers\Admin\SeoController::class, 'updateRobots'])->name('update-robots');
        Route::post('/sitemap/generate', [\App\Http\Controllers\Admin\SeoController::class, 'generateSitemap'])->name('generate-sitemap');
    });

    // Sections (index placeholders)
    Route::resource('promo-codes', \App\Http\Controllers\Admin\PromoCodeController::class)->names('promo');

    // Analytics & Bot Settings
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [AnalyticsController::class, 'index'])->name('index');
        Route::put('/bot-settings', [AnalyticsController::class, 'updateBotSettings'])->name('bot-settings.update');
        Route::post('/bot-settings/test', [AnalyticsController::class, 'testBot'])->name('bot-settings.test');
    });
});
