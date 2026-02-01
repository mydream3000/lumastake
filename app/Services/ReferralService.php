<?php

namespace App\Services;

use App\Mail\TemplatedMail;
use App\Models\Earning;
use App\Models\ReferralLevel;
use App\Models\StakingDeposit;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class ReferralService
{
    /**
     * Активирует пользователя после достижения баланса >= 1 USD и пересчитывает реферальный уровень реферера
     */
    public function activateUser(User $user): void
    {
        if ($user->active) {
            return; // Уже активирован
        }

        // Проверяем, что баланс >= 1 USD для активации
        if ($user->balance < 1) {
            return; // Недостаточно баланса для активации
        }

        $user->active = true;
        $user->saveQuietly();

        // Если у пользователя есть реферер, пересчитываем его реферальный уровень
        if ($user->referred_by) {
            $referrer = User::find($user->referred_by);
            if ($referrer) {
                $this->recalculateReferralLevel($referrer);
            }
        }
    }

    /**
     * Пересчитывает реферальный уровень пользователя на основе количества активных рефералов
     */
    public function recalculateReferralLevel(User $user): void
    {
        // Подсчитываем количество активных рефералов по факту стейкинга (≥1 активный стейк ≥ min_staking_amount)
        $activeReferralsCount = $this->countActivePartners($user);

        // Находим подходящий реферальный уровень
        $referralLevel = ReferralLevel::where('min_partners', '<=', $activeReferralsCount)
            ->orderBy('min_partners', 'desc')
            ->first();

        $user->referral_level_id = $referralLevel?->id;
        $user->saveQuietly();
    }


    /**
     * Начисляет реферальную награду при завершении стейкинга реферала (идемпотентно)
     */
    public function processReferralRewardFromProfit(User $referral, float $profitAmount, int $stakingDepositId, int $days): void
    {
        if (!$referral->referred_by) {
            return; // Нет реферера
        }

        $referrer = User::find($referral->referred_by);
        if (!$referrer) {
            return; // Реферер не найден
        }

        // Идемпотентность: проверяем, не начисляли ли уже по этому стейку
        $exists = Earning::where('user_id', $referrer->id)
            ->where('type', 'referral_reward')
            ->where('meta->staking_deposit_id', $stakingDepositId)
            ->exists();
        if ($exists) {
            return; // уже начислено
        }

        // Получаем текущее количество активных партнёров реферера
        $activeCount = $this->countActivePartners($referrer);

        // Находим подходящий реферальный уровень по количеству активных партнёров
        $referralLevel = ReferralLevel::where('min_partners', '<=', $activeCount)
            ->orderBy('min_partners', 'desc')
            ->first();
        if (!$referralLevel) {
            \Log::info('No referral level for referrer (active partners below minimum)', [
                'referrer_id' => $referrer->id,
                'active_partners' => $activeCount,
            ]);
            return; // Нет уровня — нет процента
        }

        // Рассчитываем награду от ПРОФИТА стейкинга
        $rewardPercentage = (float) $referralLevel->reward_percentage;
        $rewardAmount = round(($profitAmount * $rewardPercentage) / 100, 2);

        if ($rewardAmount <= 0) {
            return;
        }

        // Начисляем награду на баланс реферера
        $referrer->balance += $rewardAmount;
        $referrer->save();

        // Проверяем активацию реферера после увеличения баланса
        if (!$referrer->active) {
            $this->activateUser($referrer->fresh());
        }

        // Пересчитываем Tier после изменения баланса
        app(\App\Services\TierService::class)->recalculateUserTier($referrer->fresh());

        // Создаем запись в earnings для истории
        Earning::create([
            'user_id' => $referrer->id,
            'type' => 'referral_reward',
            'amount' => $rewardAmount,
            'description' => "Referral reward from {$referral->name} staking profit",
            'meta' => [
                'referral_id' => $referral->id,
                'referral_name' => $referral->name,
                'profit_amount' => $profitAmount,
                'reward_percentage' => $rewardPercentage,
                'staking_deposit_id' => $stakingDepositId,
                'staking_days' => $days,
            ],
        ]);

        // Отправляем письмо рефереру (EN, редактируемый шаблон)
        try {
            Mail::to($referrer->email)->send(new TemplatedMail(
                'referral_reward_received',
                [
                    'referrer_name' => $referrer->name,
                    'referral_name' => $referral->name,
                    'profit' => number_format($profitAmount, 2),
                    'reward_amount' => number_format($rewardAmount, 2),
                    'reward_percentage' => $rewardPercentage,
                    'staking_days' => $days,
                ],
                $referrer->id,
                'staking_deposit',
                $stakingDepositId
            ));
        } catch (\Throwable $e) {
            \Log::warning('Failed to send referral reward email', [
                'referrer_id' => $referrer->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Toast уведомление для реферера
        \App\Models\ToastMessage::createForUser(
            $referrer->id,
            'success',
            "Referral reward: {$rewardAmount} USDT ({$rewardPercentage}% from {$referral->name} staking profit)",
            route('cabinet.earnings.rewards')
        );
    }

    /**
     * Общее количество рефералов (Total Partners)
     */
    public function countTotalPartners(User $user): int
    {
        return User::where('referred_by', $user->id)->count();
    }

    /**
     * Количество активных рефералов (Active Partners):
     * реферал имеет хотя бы один активный стейк на сумму ≥ min_staking_amount
     */
    public function countActivePartners(User $user): int
    {
        $minAmount = (float) config('pools.min_staking_amount', 50);

        return User::where('referred_by', $user->id)
            ->whereHas('stakingDeposits', function ($q) use ($minAmount) {
                $q->where('status', 'active')
                  ->where('amount', '>=', $minAmount);
            })
            ->count();
    }

    /**
     * Поддерживающий метод (совместимость с прежними вызовами)
     */
    public function getActiveReferralsCount(User $user): int
    {
        return $this->countActivePartners($user);
    }

    /**
     * Получает всех рефералов пользователя (активных и неактивных)
     */
    public function getReferrals(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return User::where('referred_by', $user->id)->get();
    }
}
