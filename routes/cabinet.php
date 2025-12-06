<?php

use App\Http\Controllers\Cabinet\CalculatorController;
use App\Http\Controllers\Cabinet\ContactController;
use App\Http\Controllers\Cabinet\DashboardController;
use App\Http\Controllers\Cabinet\DepositController;
use App\Http\Controllers\Cabinet\EarningController;
use App\Http\Controllers\Cabinet\HistoryController;
use App\Http\Controllers\Cabinet\PoolsController;
use App\Http\Controllers\Cabinet\ProfileController;
use App\Http\Controllers\Cabinet\ReferralController;
use App\Http\Controllers\Cabinet\StakingController;
use App\Http\Controllers\Cabinet\ToastController;
use App\Http\Controllers\Cabinet\TransactionController;
use App\Http\Controllers\Cabinet\WithdrawController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Cabinet Routes
|--------------------------------------------------------------------------
|
| Here is where you can register cabinet routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('cabinet.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profit-by-period', [DashboardController::class, 'getProfitByPeriod'])->name('dashboard.profit-by-period');
    Route::get('/balance/state', [DashboardController::class, 'getBalanceState'])->name('dashboard.balance-state');

    // Calculator
    Route::post('/calculator/calculate', [CalculatorController::class, 'calculate'])->name('calculator.calculate');
    Route::post('/calculator/pools', [CalculatorController::class, 'getPools'])->name('calculator.pools');

    // Pools
    Route::get('/pools', [PoolsController::class, 'index'])->name('pools.index');
    Route::get('/pools/data', [PoolsController::class, 'getData'])->name('pools.data');

    // Deposit
    Route::get('/deposit', [DepositController::class, 'index'])->name('deposit');
    Route::post('/deposit/test', [DepositController::class, 'testDeposit'])->name('deposit.test');
    Route::post('/deposit/accept-usdt', [DepositController::class, 'acceptUSDT'])->name('deposit.accept-usdt');
    Route::post('/deposit/confirm-payment', [DepositController::class, 'confirmPayment'])->name('deposit.confirm-payment');

    // Withdraw
    Route::get('/withdraw', [WithdrawController::class, 'index'])->name('withdraw');
    Route::post('/withdraw', [WithdrawController::class, 'request'])->name('withdraw.request');
    Route::post('/withdraw/confirm', [WithdrawController::class, 'confirm'])->name('withdraw.confirm');
    Route::post('/withdraw/resend-code', [WithdrawController::class, 'resendCode'])->name('withdraw.resend-code');

    // Staking
    Route::get('/staking', [StakingController::class, 'index'])->name('staking.index');
    Route::get('/staking/data', [StakingController::class, 'getData'])->name('staking.data');
    Route::post('/staking', [StakingController::class, 'store'])->name('staking.store');
    Route::post('/staking/create', [StakingController::class, 'create'])->name('staking.create');
    Route::post('/staking/{id}/auto-renewal', [StakingController::class, 'toggleAutoRenewal'])->name('staking.auto-renewal');
    Route::post('/staking/{id}/unstake', [StakingController::class, 'unstake'])->name('staking.unstake');

    // History
    Route::get('/history', [HistoryController::class, 'index'])->name('history');
    Route::get('/history/data', [HistoryController::class, 'getData'])->name('history.data');

    // Transactions
    Route::get('/transactions/deposits', [TransactionController::class, 'deposits'])->name('transactions.deposits');
    Route::get('/transactions/deposits/data', [TransactionController::class, 'getDepositsData'])->name('transactions.deposits.data');
    Route::get('/transactions/withdraw', [TransactionController::class, 'withdraw'])->name('transactions.withdraw');
    Route::get('/transactions/withdraw/data', [TransactionController::class, 'getWithdrawData'])->name('transactions.withdraw.data');
    Route::get('/transactions/{id}/details', [TransactionController::class, 'getTransactionDetails'])->name('transactions.details');
    Route::post('/transactions/withdraw/{id}/cancel', [TransactionController::class, 'cancelWithdraw'])->name('transactions.withdraw.cancel');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/verify-email-change', [ProfileController::class, 'verifyEmailChange'])->name('profile.verify-email-change');
    Route::post('/profile/resend-email-code', [ProfileController::class, 'resendEmailChangeCode'])->name('profile.resend-email-code');
    Route::post('/profile/send-email-verification', [ProfileController::class, 'sendEmailVerification'])->middleware('throttle:3,1')->name('profile.send-email-verification');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->middleware('throttle:5,1')->name('profile.change-password');

    // Referrals
    Route::get('/referrals', [ReferralController::class, 'index'])->name('referrals');
    Route::get('/referrals/data', [ReferralController::class, 'getData'])->name('referrals.data');

    // Rewards
    Route::get('/rewards', [ReferralController::class, 'rewards'])->name('rewards');

    // Earnings
    Route::get('/earnings/profit', [EarningController::class, 'profit'])->name('earnings.profit');
    Route::get('/earnings/profit/data', [EarningController::class, 'getProfitData'])->name('earnings.profit.data');
    Route::get('/earnings/rewards', [EarningController::class, 'rewards'])->name('earnings.rewards');
    Route::get('/earnings/rewards/data', [EarningController::class, 'getRewardsData'])->name('earnings.rewards.data');

    // Contact / Feedback
    Route::get('/feedback', [ContactController::class, 'index'])->name('feedback');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:3,10');

    // Toast Messages
    Route::get('/toasts', [ToastController::class, 'index'])->name('toasts.index');
});
