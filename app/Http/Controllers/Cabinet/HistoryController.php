<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\StakingDeposit;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = StakingDeposit::with('tier')
            ->where('user_id', auth()->id())
            ->orderByRaw("CASE WHEN status = 'active' THEN 1 ELSE 0 END")
            ->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('start_date', '<=', $request->date_to);
        }

        $deposits = $query->paginate(20);

        return view('cabinet.history', compact('deposits'));
    }

    public function getData(Request $request)
    {
        $userId = auth()->id();

        $query = StakingDeposit::with('tier')
            ->where('user_id', $userId)
            ->whereIn('status', ['completed', 'unstaked', 'cancelled'])
            ->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('start_date', '<=', $request->date_to);
        }

        $deposits = $query->get();

        // Получаем все стейки пользователя для нумерации в рамках каждого tier
        $allUserStakes = StakingDeposit::where('user_id', $userId)->orderBy('id')->get();
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
            'data' => $deposits->map(function ($deposit) use ($stakeNumbers) {
                // Для active статуса рассчитываем теоретический профит, для остальных берем earned_profit
                if ($deposit->status === 'active') {
                    $profitValue = $deposit->amount * ($deposit->percentage / 100);
                    $profitFormatted = '$' . number_format($profitValue, 2);
                } else {
                    $profitValue = $deposit->earned_profit;
                    // Форматирование с минусом перед долларом для отрицательных значений
                    if ($profitValue < 0) {
                        $profitFormatted = '-$' . number_format(abs($profitValue), 2);
                    } else {
                        $profitFormatted = '$' . number_format($profitValue, 2);
                    }
                }

                $tierName = $deposit->tier->name ?? 'Pool';
                $number = $stakeNumbers[$deposit->id] ?? 1;

                return [
                    'id' => $deposit->id,
                    'pool_name' => $tierName . ' №' . $number,
                    'duration' => $deposit->days . ' days',
                    'profit' => number_format($deposit->percentage, 2),
                    'amount' => '$' . number_format($deposit->amount, 2),
                    'earned_profit' => $profitFormatted,
                    'earned_profit_raw' => $profitValue,
                    'start_date' => $deposit->start_date->toISOString(),
                    'end_date' => $deposit->end_date->toISOString(),
                    'status' => $deposit->status,
                    'highlight' => $deposit->status === 'completed',
                ];
            })
        ]);
    }
}
