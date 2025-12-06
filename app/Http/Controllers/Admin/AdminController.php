<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CryptoTransaction;
use App\Models\StakingDeposit;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Статистика пользователей
        $totalUsers = User::count();
        $activeUsers = User::where('active', true)->count();
        $newUsersToday = User::whereDate('created_at', today())->count();
        $newUsersThisWeek = User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $newUsersThisMonth = User::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();

        // Статистика балансов
        $totalBalance = User::sum('balance');

        // Статистика стейкинга
        $activeStakes = StakingDeposit::where('status', 'active')->count();
        $totalStaked = StakingDeposit::where('status', 'active')->sum('amount');
        $completedStakes = StakingDeposit::where('status', 'completed')->count();
        $pendingStakes = StakingDeposit::where('status', 'pending')->count();

        // Порог подтверждений по сетям из конфига
        $minConf = config('crypto.min_confirmations', []);

        // Хелпер для условий по подтверждениям в разных сетях
        $confirmedOnChain = function ($q) use ($minConf) {
            $q->where(function ($netQ) use ($minConf) {
                if (empty($minConf)) {
                    $netQ->where('confirmations', '>=', 1);
                    return;
                }
                foreach ($minConf as $net => $min) {
                    $netQ->orWhere(function ($qq) use ($net, $min) {
                        $qq->where('network', $net)->where('confirmations', '>=', (int) $min);
                    });
                }
            });
        };

        // Реальные депозиты (USDT + USDC) — всего и за сегодня
        $totalRealDeposits = CryptoTransaction::query()
            ->where('processed', true)
            ->whereIn('token', ['USDT', 'USDC'])
            ->where($confirmedOnChain)
            ->sum('amount');

        $realDepositsToday = CryptoTransaction::query()
            ->where('processed', true)
            ->whereIn('token', ['USDT', 'USDC'])
            ->where($confirmedOnChain)
            ->whereDate('created_at', today())
            ->sum('amount');

        // Статистика транзакций (оставляем выводы пэндингов)
        $pendingDeposits = 0; // заменено на $totalRealDeposits в интерфейсе
        $pendingWithdrawals = Transaction::where('type', 'withdraw')
            ->where('status', 'pending')
            ->count();
        $pendingWithdrawalsAmount = Transaction::where('type', 'withdraw')
            ->where('status', 'pending')
            ->sum('amount');

        // Было: completedDepositsToday по таблице Transaction. Заменяем на realDepositsToday из ончейна.
        $completedDepositsToday = $realDepositsToday;

        $completedWithdrawalsToday = Transaction::where('type', 'withdraw')
            ->where('status', 'confirmed')
            ->whereDate('created_at', today())
            ->sum('amount');

        // Статистика новых депозиторов (клиенты, сделавшие первый депозит)
        $newDepositorsToday = Transaction::where('type', 'deposit')
            ->where('status', 'confirmed')
            ->whereDate('created_at', today())
            ->distinct()
            ->count('user_id');

        $newDepositorsThisWeek = Transaction::where('type', 'deposit')
            ->where('status', 'confirmed')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->distinct()
            ->count('user_id');

        $newDepositorsThisMonth = Transaction::where('type', 'deposit')
            ->where('status', 'confirmed')
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->distinct()
            ->count('user_id');

        // Последние регистрации (топ 5)
        $recentUsers = User::orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'name', 'email', 'balance', 'created_at', 'active']);

        // Последние депозиты (топ 5)
        $recentDeposits = Transaction::with('user:id,name,email')
            ->where('type', 'deposit')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'user_id', 'amount', 'status', 'created_at']);

        // Последние выводы (топ 5)
        $recentWithdrawals = Transaction::with('user:id,name,email')
            ->where('type', 'withdraw')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'user_id', 'amount', 'status', 'created_at']);

        // Последние стейки (топ 5)
        $recentStakes = StakingDeposit::with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'user_id', 'amount', 'days', 'status', 'created_at']);

        // График регистраций за последние 7 дней
        $registrationsChart = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [now()->subDays(7), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // График депозитов за последние 7 дней
        $depositsChart = Transaction::selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->where('type', 'deposit')
            ->where('status', 'confirmed')
            ->whereBetween('created_at', [now()->subDays(7), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        // График стейкинга за последние 7 дней
        $stakingChart = StakingDeposit::selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->whereBetween('created_at', [now()->subDays(7), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'newUsersToday',
            'newUsersThisWeek',
            'newUsersThisMonth',
            'totalBalance',
            'activeStakes',
            'totalStaked',
            'completedStakes',
            'pendingStakes',
            'pendingDeposits',
            'pendingWithdrawals',
            'pendingWithdrawalsAmount',
            'completedDepositsToday',
            'totalRealDeposits',
            'realDepositsToday',
            'completedWithdrawalsToday',
            'newDepositorsToday',
            'newDepositorsThisWeek',
            'newDepositorsThisMonth',
            'recentUsers',
            'recentDeposits',
            'recentWithdrawals',
            'recentStakes',
            'registrationsChart',
            'depositsChart',
            'stakingChart'
        ));
    }
}
