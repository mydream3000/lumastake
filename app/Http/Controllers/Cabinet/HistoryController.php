<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\StakingDeposit;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = StakingDeposit::where('user_id', auth()->id())
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
        $query = StakingDeposit::where('user_id', auth()->id())
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

        return response()->json([
            'success' => true,
            'data' => $deposits->map(function ($deposit) {
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

                return [
                    'id' => $deposit->id,
                    'pool_name' => 'Pool #' . $deposit->id,
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
