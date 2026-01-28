<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Tier;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getProfitByPeriod(Request $request)
    {
        $period = $request->input('period', 'week');
        $user = auth()->user();

        $startDate = match($period) {
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfWeek(),
        };

        // Completed profit in period
        $completedProfit = $user->stakingDeposits()
            ->where('status', 'completed')
            ->where('completed_at', '>=', $startDate)
            ->sum('earned_profit');

        // Expected profit from active stakings
        $activeStakings = $user->stakingDeposits()
            ->where('status', 'active')
            ->where('start_date', '>=', $startDate)
            ->get();

        $expectedProfit = $activeStakings->sum(function ($deposit) {
            return round($deposit->amount * $deposit->percentage / 100, 2);
        });

        // Other amount
        $totalInvested = $user->deposited;
        $otherAmount = max(0, $totalInvested - $completedProfit - $expectedProfit);

        // Fallback: ensure the chart has at least a background ring if empty
        if ($completedProfit == 0 && $expectedProfit == 0 && $otherAmount == 0) {
            $otherAmount = 1;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'completed' => (float) $completedProfit,
                'expected' => (float) $expectedProfit,
                'other' => (float) $otherAmount,
                'total' => (float) ($completedProfit + $expectedProfit),
            ]
        ]);
    }

    public function index()
    {
        $user = auth()->user();

        // Расчёт текущего tier
        $totalAmount = $user->balance + $user->stakingDeposits()
            ->where('status', 'active')->sum('amount');
        $currentTier = Tier::where('min_balance', '<=', $totalAmount)
            ->orderBy('level', 'desc')->first();

        // Если tier не найден (например, для нулевого баланса), берем STARTER (level 0)
        if (!$currentTier) {
            $currentTier = Tier::orderBy('level')->first();
        }

        // Прогресс до следующего tier
        $nextTier = $currentTier
            ? Tier::where('level', '>', $currentTier->level)->orderBy('level')->first()
            : null;

        $progress = 0;
        if ($currentTier && $nextTier) {
            $progress = ($totalAmount - $currentTier->min_balance) /
                ($nextTier->min_balance - $currentTier->min_balance);
        } elseif ($currentTier && !$nextTier) {
            $progress = 1;
        }

        // Остаток до следующего tier
        $remainingToNextTier = 0;
        if ($currentTier && $nextTier) {
            $remainingToNextTier = max(0, $nextTier->min_balance - $totalAmount);
        }

        // Investment pools (пулы стейкинга) - только для текущего tier
        if ($user->account_type === 'islamic') {
            $pools = $currentTier
                ? $currentTier->islamicInvestmentPools()
                    ->orderBy('duration_days')
                    ->get()
                    ->map(function ($pool) {
                        return [
                            'id' => $pool->id,
                            'name' => $pool->tier->name . ' ' . $pool->duration_days . ' days',
                            'days' => $pool->duration_days,
                            'min_stake' => 50, // Assuming a default, adjust if needed
                            'percentage_label' => $pool->min_percentage . '% - ' . $pool->max_percentage . '%',
                            'min_percentage' => $pool->min_percentage,
                            'max_percentage' => $pool->max_percentage,
                        ];
                    })
                    ->toArray()
                : [];
        } else {
            $pools = $currentTier
                ? $currentTier->investmentPools()
                    ->where('is_active', true)
                    ->orderBy('order')
                    ->get()
                    ->map(function ($pool) {
                        return [
                            'id' => $pool->id,
                            'name' => $pool->name,
                            'days' => $pool->days,
                            'min_stake' => $pool->min_stake,
                            'percentage_label' => rtrim(rtrim(number_format($pool->percentage, 2), '0'), '.') . '%',
                            'percentage' => $pool->percentage,
                        ];
                    })
                    ->toArray()
                : [];
        }

        // All Tiers Data for Calculator Logic
        $allTiers = Tier::with($user->account_type === 'islamic' ? 'islamicInvestmentPools' : 'investmentPools')
            ->orderBy('min_balance')
            ->get()
            ->map(function ($tier) use ($user) {
                $pools = $user->account_type === 'islamic'
                    ? $tier->islamicInvestmentPools
                    : $tier->investmentPools->where('is_active', true);

                return [
                    'name' => $tier->name,
                    'min_balance' => $tier->min_balance,
                    'pools' => $pools->map(function ($pool) use ($user) {
                        return $user->account_type === 'islamic' ? [
                            'id' => $pool->id,
                            'days' => $pool->duration_days,
                            'name' => $pool->tier->name . ' ' . $pool->duration_days . ' days',
                            'min_percentage' => $pool->min_percentage,
                            'max_percentage' => $pool->max_percentage,
                        ] : [
                            'id' => $pool->id,
                            'days' => $pool->days,
                            'name' => $pool->name,
                            'percentage' => $pool->percentage,
                        ];
                    })->values()->toArray()
                ];
            });


        // Данные для графика прибыли
        // Оранжевый: полученный профит (уже на балансе из completed стейкингов)
        $completedProfit = $user->stakingDeposits()
            ->where('status', 'completed')
            ->sum('earned_profit');

        // Блекло-оранжевый: ожидаемый профит от активных стейкингов
        $activeStakings = $user->stakingDeposits()
            ->where('status', 'active')
            ->get();

        $expectedProfit = $activeStakings->sum(function ($deposit) {
            return round($deposit->amount * $deposit->percentage / 100, 2);
        });

        // Серый: остальная часть (можно показать общую инвестированную сумму)
        $totalInvested = $user->deposited;
        $otherAmount = max(0, $totalInvested - $completedProfit - $expectedProfit);

        if ($completedProfit == 0 && $expectedProfit == 0 && $otherAmount == 0) {
            $otherAmount = 1;
        }

        $profitChartData = [
            'completed' => (float) $completedProfit,
            'expected' => (float) $expectedProfit,
            'other' => (float) $otherAmount,
        ];

        return view('cabinet.dashboard', compact(
            'user', 'currentTier', 'nextTier', 'progress', 'remainingToNextTier', 'pools', 'profitChartData', 'allTiers'
        ));
    }

    /**
     * Lightweight balance state endpoint for client sync
     */
    public function getBalanceState(Request $request)
    {
        $user = auth()->user()->fresh();

        // Pending amount reserved by withdrawals includes BOTH:
        // 1) Non-expired PendingWithdrawal (user requested code but not confirmed yet)
        // 2) Pending withdraw Transactions (created and awaiting admin)
        $pendingAmountFromCodes = (float) \App\Models\PendingWithdrawal::where('user_id', $user->id)
            ->where('code_expires_at', '>=', now())
            ->sum('amount');

        $pendingAmountFromTransactions = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'withdraw')
            ->where('status', 'pending')
            ->sum('amount');

        $pendingAmount = $pendingAmountFromCodes + $pendingAmountFromTransactions;

        return response()->json([
            'success' => true,
            'balance' => (float) $user->balance,
            'available_balance' => (float) $user->available_balance,
            'has_pending_withdraw' => $pendingAmount > 0,
            'pending_withdraw_amount' => $pendingAmount,
            'total_staked' => (float) $user->stakingDeposits()->where('status', 'active')->sum('amount'),
        ]);
    }
}
