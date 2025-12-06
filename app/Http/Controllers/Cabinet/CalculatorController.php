<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Tier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalculatorController extends Controller
{
    /**
     * Calculate tier and percentage by amount and duration
     */
    public function calculate(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'days' => 'required|integer|in:10,30,60,90,180',
        ]);

        $amount = (float) $request->amount;
        $days = (int) $request->days;

        // Определяем tier по сумме
        $tier = Tier::where('min_balance', '<=', $amount)
            ->where(function ($query) use ($amount) {
                $query->whereNull('max_balance')
                    ->orWhere('max_balance', '>=', $amount);
            })
            ->orderBy('level', 'desc')
            ->first();

        // Если tier не найден, берем самый первый (STARTER)
        if (!$tier) {
            $tier = Tier::orderBy('level')->first();
        }

        // Получаем процент для выбранного периода
        $tierPercentage = $tier->percentages()
            ->where('days', $days)
            ->first();

        $percentage = $tierPercentage ? $tierPercentage->percentage : 0;

        // Рассчитываем прибыль
        $profit = ($amount * $percentage) / 100;
        $totalReturn = $amount + $profit;

        return response()->json([
            'success' => true,
            'tier' => [
                'id' => $tier->id,
                'name' => $tier->name,
                'level' => $tier->level,
                'min_balance' => $tier->min_balance,
                'max_balance' => $tier->max_balance,
            ],
            'percentage' => $percentage,
            'profit' => round($profit, 2),
            'total_return' => round($totalReturn, 2),
        ]);
    }

    /**
     * Get all available pools with percentages for given amount
     */
    public function getPools(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $amount = (float) $request->amount;

        // Определяем tier по сумме
        $tier = Tier::where('min_balance', '<=', $amount)
            ->where(function ($query) use ($amount) {
                $query->whereNull('max_balance')
                    ->orWhere('max_balance', '>=', $amount);
            })
            ->orderBy('level', 'desc')
            ->first();

        if (!$tier) {
            $tier = Tier::orderBy('level')->first();
        }

        $poolDays = [10, 30, 60, 90, 180];
        $pools = [];

        foreach ($poolDays as $days) {
            $tierPercentage = $tier->percentages()->where('days', $days)->first();

            $pools[] = [
                'days' => $days,
                'percentage' => $tierPercentage ? $tierPercentage->percentage : 0,
            ];
        }

        return response()->json([
            'success' => true,
            'tier' => [
                'id' => $tier->id,
                'name' => $tier->name,
                'level' => $tier->level,
            ],
            'pools' => $pools,
        ]);
    }
}
