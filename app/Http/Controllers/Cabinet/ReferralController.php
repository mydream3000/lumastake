<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\ReferralLevel;
use App\Models\Earning;
use App\Models\User;
use App\Services\ReferralService;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $referrals = User::where('referred_by', $user->id)->get();

        $referralEarnings = Transaction::where('user_id', $user->id)
            ->where('type', 'reward')
            ->whereJsonContains('meta->source', 'referral')
            ->sum('amount');

        return view('cabinet.referrals', compact('referrals', 'referralEarnings'));
    }

    public function getData()
    {
        $user = auth()->user();
        $referrals = User::where('referred_by', $user->id)->get();

        return response()->json([
            'success' => true,
            'data' => $referrals->map(function ($referral) {
                return [
                    'id' => $referral->id,
                    'name' => $referral->name,
                    'email' => $referral->email,
                    'created_at' => $referral->created_at->format('Y-m-d H:i:s'),
                ];
            })
        ]);
    }

    public function rewards(ReferralService $referralService)
    {
        $user = auth()->user();

        // Принудительно синхронизируем referral_level_id с фактическим числом активных партнёров
        // Это гарантирует корректную подсветку уровней даже при задержках фоновых джобов
        $referralService->recalculateReferralLevel($user);
        $user->refresh();

        // Счётчики через сервис (динамически по стейкингу ≥ min_staking_amount)
        $activeReferrals = $referralService->countActivePartners($user);
        $totalReferrals = $referralService->countTotalPartners($user);

        // Список рефералов для блока "Referred Partners" (нужны аватары)
        $referrals = User::where('referred_by', $user->id)
            ->select('id', 'name', 'email', 'avatar')
            ->get();

        // Реферальная ссылка
        $referralLink = url('/register?ref=' . $user->uuid);

        // Все уровни рефералов
        $referralLevels = ReferralLevel::orderBy('level')->get();

        // Текущий level для подсветки (поле referral_level_id хранит id записи, получаем её level)
        $currentLevel = null;
        if ($user->referral_level_id) {
            $levelModel = ReferralLevel::find($user->referral_level_id);
            $currentLevel = $levelModel?->level;
        }

        // Общая сумма реферальных наград из Earning (type=referral_reward)
        $referralRewardsSum = Earning::referralReward()
            ->where('user_id', $user->id)
            ->sum('amount');

        return view('cabinet.rewards', compact(
            'totalReferrals',
            'activeReferrals',
            'referrals',
            'referralLink',
            'referralLevels',
            'currentLevel',
            'referralRewardsSum'
        ));
    }
}
