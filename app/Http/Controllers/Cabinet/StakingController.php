<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessStakingJob;
use App\Jobs\ProcessUnstakingJob;
use App\Models\InvestmentPoolIslamic;
use App\Models\StakingDeposit;
use App\Models\Tier;
use App\Models\TierPercentage;
use App\Models\ToastMessage;
use Illuminate\Http\Request;

class StakingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        // Показываем только активные стейкинги, которые еще не истекли
        $stakes = $user->stakingDeposits()
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->latest()
            ->paginate(10);

        return view('cabinet.staking.index', compact('user', 'stakes'));
    }

    public function getData()
    {
        $user = auth()->user();
        // Фильтруем только активные стейкинги, которые еще не истекли
        // Истекшие будут обработаны scheduler'ом каждые 5 минут
        $stakes = $user->stakingDeposits()
            ->with('tier')
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->latest()
            ->get();

        // Получаем все стейки пользователя для нумерации в рамках каждого tier
        $allUserStakes = $user->stakingDeposits()->orderBy('id')->get();
        $tierCounters = [];
        $stakeNumbers = [];
        foreach ($allUserStakes as $s) {
            $tierId = $s->tier_id;
            if (!isset($tierCounters[$tierId])) {
                $tierCounters[$tierId] = 0;
            }
            $tierCounters[$tierId]++;
            $stakeNumbers[$s->id] = $tierCounters[$tierId];
        }

        return response()->json([
            'success' => true,
            'data' => $stakes->map(function ($stake) use ($user, $stakeNumbers) {
                $now = now();
                // Вычисляем разницу в секундах (положительное = в будущем, отрицательное = в прошлом)
                $timeLeftSeconds = $stake->end_date->timestamp - $now->timestamp;

                $profitDisplay = number_format($stake->percentage, 2) . '%';

                if ($user->account_type === 'islamic') {
                    if ($stake->percentage == 0) {
                         // Look up the pool range
                         $pool = InvestmentPoolIslamic::where('tier_id', $stake->tier_id)
                             ->where('duration_days', $stake->days)
                             ->first();
                         if ($pool) {
                             $profitDisplay = $pool->min_percentage . '% - ' . $pool->max_percentage . '%';
                         } else {
                             $profitDisplay = 'Pending';
                         }
                    } else {
                         // Even if percentage is set (e.g. completed), we might want to show range?
                         // But active stakes with 0 percentage are the ones we care about.
                         // If percentage is set, it means it's calculated (maybe manual?).
                         // For active, it should be 0.
                    }
                }

                $tierName = $stake->tier->name ?? 'Pool';
                $number = $stakeNumbers[$stake->id] ?? 1;

                return [
                    'id' => $stake->id,
                    'pool_name' => $tierName . ' №' . $number,
                    'duration' => $stake->days . ' days',
                    'profit' => $profitDisplay,
                    'amount' => '$' . number_format($stake->amount, 2),
                    'auto_renewal' => $stake->auto_renewal,
                    'start_date' => $stake->start_date->toISOString(),
                    'time_left' => $timeLeftSeconds,
                ];
            })
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $minAmount = config('pools.min_staking_amount', 50);

        $request->validate([
            'amount' => "required|numeric|min:{$minAmount}|max:" . $user->available_balance,
            'days' => 'required|in:' . implode(',', config('pools.durations')),
            'auto_renewal' => 'boolean',
        ]);

        if ($request->amount > $user->available_balance) {
            return back()->withErrors(['amount' => 'Insufficient balance']);
        }

        // Определение текущего tier
        $totalAmount = $user->balance + $user->stakingDeposits()
            ->where('status', 'active')->sum('amount');
        $currentTier = Tier::where('min_balance', '<=', $totalAmount)
            ->orderBy('level', 'desc')->first();

        // Получение процента
        $percentage = TierPercentage::where('tier_id', $currentTier->id)
            ->where('days', $request->days)->value('percentage');

        // Создание депозита
        StakingDeposit::create([
            'user_id' => $user->id,
            'tier_id' => $currentTier->id,
            'amount' => $request->amount,
            'days' => $request->days,
            'percentage' => $percentage,
            'start_date' => now(),
            'end_date' => now()->addDays($request->days),
            'auto_renewal' => $request->auto_renewal ?? false,
            'status' => 'active',
        ]);

        $user->decrement('balance', $request->amount);

        return redirect()->route('cabinet.history')
            ->with('success', 'Staking deposit created successfully');
    }

    public function toggleAutoRenewal($id)
    {
        $stake = StakingDeposit::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->firstOrFail();

        $stake->auto_renewal = request()->input('auto_renewal', false);
        $stake->save();

        return response()->json([
            'success' => true,
            'message' => 'Auto renewal updated successfully'
        ]);
    }

    public function create(Request $request)
    {
        $minAmount = config('pools.min_staking_amount', 50);

        $request->validate([
            'amount' => "required|numeric|min:{$minAmount}",
            'auto_stake' => 'boolean',
        ]);

        $user = auth()->user();

        if ($user->account_type === 'islamic') {
            $request->validate([
                'pool_id' => 'required|exists:investment_pools_islamic,id',
            ]);
            $pool = InvestmentPoolIslamic::findOrFail($request->pool_id);
            $minStake = config('pools.min_staking_amount', 50);
            $percentage = $pool->min_percentage; // Job will recalculate random range
            $tierId = $pool->tier_id;
            $days = $pool->duration_days;
        } else {
            $request->validate([
                'pool_id' => 'required|exists:investment_pools,id',
            ]);
            $pool = \App\Models\InvestmentPool::findOrFail($request->pool_id);
            $minStake = $pool->min_stake;
            $percentage = $pool->percentage;
            $tierId = $pool->tier_id;
            $days = $pool->days;
        }

        // Проверяем доступный баланс (исключая суммы, поставленные на вывод)
        if ($user->available_balance < $request->amount) {
            return response()->json([
                'message' => 'Insufficient balance',
            ], 422);
        }

        // Проверяем минимальную сумму для этого пула
        if ($request->amount < $minStake) {
            return response()->json([
                'message' => 'Minimum stake for this pool is $' . number_format($minStake, 2),
            ], 422);
        }

        // Создаем информационное уведомление
        ToastMessage::createForUser(
            $user->id,
            'info',
            'Your staking request is being processed...'
        );

        // Запускаем Job для обработки
        ProcessStakingJob::dispatch(
            $user->id,
            $tierId,
            $request->amount,
            $days,
            $percentage,
            $request->auto_stake ?? false
        );

        return response()->json([
            'success' => true,
            'message' => 'Staking request submitted',
        ]);
    }

    public function unstake($id)
    {
        $stake = StakingDeposit::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->firstOrFail();

        $user = auth()->user();

        // Создаем информационное уведомление
        ToastMessage::createForUser(
            $user->id,
            'info',
            'Your unstaking request is being processed...'
        );

        // Запускаем Job для обработки
        ProcessUnstakingJob::dispatch($stake->id);

        return response()->json([
            'success' => true,
            'message' => 'Unstaking request submitted'
        ]);
    }
}
