<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Earning;
use Illuminate\Http\Request;

class EarningController extends Controller
{
    /**
     * Страница заработка от стейкингов
     */
    public function profit()
    {
        return view('cabinet.earnings.profit');
    }

    /**
     * Данные для таблицы Profit Earnings
     */
    public function getProfitData(Request $request)
    {
        $query = Earning::where('user_id', auth()->id())
            ->where('type', 'profit')
            ->orderBy('created_at', 'desc');

        $earnings = $query->paginate(15);

        // Добавляем связанные данные стейкингов
        $earnings->getCollection()->transform(function ($earning) {
            return [
                'id' => $earning->id,
                'type' => 'Staking Profit',
                'invested_amount' => number_format($earning->meta['principal_amount'] ?? 0, 2),
                'earned' => number_format($earning->amount, 2),
                'pool' => $earning->meta['tier_name'] ?? 'N/A',
                'duration' => ($earning->meta['days'] ?? 0) . ' days',
                'tier' => $earning->meta['tier_name'] ?? 'N/A',
                'profit' => number_format($earning->meta['percentage'] ?? 0, 2),
                'created_at' => $earning->created_at->format('d, M, Y'),
                'description' => $earning->description,
            ];
        });

        return response()->json($earnings);
    }

    /**
     * Страница заработка от рефералов
     */
    public function rewards()
    {
        return view('cabinet.earnings.rewards');
    }

    /**
     * Данные для таблицы Referral Rewards
     */
    public function getRewardsData(Request $request)
    {
        $query = Earning::where('user_id', auth()->id())
            ->where('type', 'referral_reward')
            ->orderBy('created_at', 'desc');

        $earnings = $query->paginate(15);

        // Добавляем данные рефералов
        $earnings->getCollection()->transform(function ($earning) {
            return [
                'id' => $earning->id,
                'date' => $earning->created_at->format('d, M, Y'),
                'referral_name' => $earning->referral_name,
                'profit_amount' => $earning->meta['profit_amount'] ?? null,
                'reward_percentage' => $earning->reward_percentage,
                'amount' => $earning->amount,
                'description' => $earning->description,
            ];
        });

        return response()->json($earnings);
    }
}
