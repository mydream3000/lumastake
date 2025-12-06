<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\InvestmentPool;
use App\Models\Tier;
use Illuminate\Http\Request;

class PoolsController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Определяем текущий tier пользователя
        $totalAmount = $user->balance + $user->stakingDeposits()
            ->where('status', 'active')->sum('amount');
        $currentTier = Tier::where('min_balance', '<=', $totalAmount)
            ->orderBy('level', 'desc')->first();

        // Если tier не найден, берем STARTER (level 1)
        if (!$currentTier) {
            $currentTier = Tier::orderBy('level')->first();
        }

        if ($user->account_type === 'islamic') {
            $pools = $currentTier
                ? $currentTier->islamicInvestmentPools()
                    ->orderBy('duration_days')
                    ->get()
                    ->map(function ($pool) {
                        return [
                            'name' => $pool->tier->name . ' ' . $pool->duration_days . ' days',
                            'days' => $pool->duration_days,
                            'min_stake' => config('pools.min_staking_amount', 50),
                            'percentage_label' => $pool->min_percentage . '% - ' . $pool->max_percentage . '%',
                        ];
                    })
                : collect();
        } else {
            // Get investment pools ONLY for current tier
            $pools = $currentTier
                ? InvestmentPool::where('is_active', true)
                    ->where('tier_id', $currentTier->id)
                    ->orderBy('order')
                    ->get()
                    ->map(function ($pool) {
                        return [
                            'name' => $pool->name,
                            'days' => $pool->days,
                            'min_stake' => $pool->min_stake,
                            'percentage_label' => $pool->percentage . '%',
                            'percentage' => $pool->percentage,
                        ];
                    })
                : collect();
        }

        return view('cabinet.pools.index', compact('user', 'pools'));
    }

    public function getData()
    {
        $user = auth()->user();

        // Определяем текущий tier пользователя
        $totalAmount = $user->balance + $user->stakingDeposits()
            ->where('status', 'active')->sum('amount');
        $currentTier = Tier::where('min_balance', '<=', $totalAmount)
            ->orderBy('level', 'desc')->first();

        // Если tier не найден, берем STARTER (level 1)
        if (!$currentTier) {
            $currentTier = Tier::orderBy('level')->first();
        }

        if ($user->account_type === 'islamic') {
            $pools = $currentTier
                ? $currentTier->islamicInvestmentPools()
                    ->orderBy('duration_days')
                    ->get()
                : collect();

            return response()->json([
                'success' => true,
                'data' => $pools->map(function ($pool) {
                    $minStake = config('pools.min_staking_amount', 50);
                    return [
                        'id' => $pool->id,
                        'name' => $pool->tier->name . ' ' . $pool->duration_days . ' days',
                        'days' => $pool->duration_days,
                        // Keep a formatted string for UI and a numeric value for logic
                        'min_stake' => '$' . number_format($minStake, 2),
                        'min_stake_value' => (float) $minStake,
                        'profit' => $pool->min_percentage . '% - ' . $pool->max_percentage . '%',
                    ];
                })
            ]);
        } else {
            // Get investment pools ONLY for current tier
            $pools = $currentTier
                ? InvestmentPool::where('is_active', true)
                    ->where('tier_id', $currentTier->id)
                    ->orderBy('order')
                    ->get()
                : collect();

            return response()->json([
                'success' => true,
                'data' => $pools->map(function ($pool) {
                    return [
                        'id' => $pool->id,
                        'name' => $pool->name,
                        'days' => $pool->days,
                        // Keep a formatted string for UI and a numeric value for logic
                        'min_stake' => '$' . number_format($pool->min_stake, 2),
                        'min_stake_value' => (float) $pool->min_stake,
                        'profit' => $pool->percentage . '%',
                    ];
                })
            ]);
        }
    }
}
