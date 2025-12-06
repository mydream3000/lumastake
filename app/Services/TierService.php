<?php

namespace App\Services;

use App\Models\Tier;
use App\Models\User;

class TierService
{
    /**
     * Рассчитывает и обновляет тир пользователя на основе его баланса и активных стейкингов
     *
     * Формула: balance + sum(amount всех активных staking)
     */
    public function recalculateUserTier(User $user): void
    {
        // Получаем сумму всех активных стейкингов
        $activeStakingTotal = $user->stakingDeposits()
            ->where('status', 'active')
            ->sum('amount');

        // Рассчитываем общую сумму для определения тира
        $totalAmount = $user->balance + $activeStakingTotal;

        // Находим подходящий тир
        $tier = Tier::where('min_balance', '<=', $totalAmount)
            ->orderBy('min_balance', 'desc')
            ->first();

        if ($tier) {
            // Присваиваем найденный tier
            $user->current_tier = $tier->id;
        } else {
            // Если сумма ниже минимального порога первого tier — сбрасываем до "No Tier"
            $user->current_tier = null;
        }

        // Сохраняем без вызова событий, чтобы избежать рекурсии наблюдателей
        $user->saveQuietly();
    }

    /**
     * Получает тир пользователя
     */
    public function getUserTier(User $user): ?Tier
    {
        return Tier::find($user->current_tier);
    }

    /**
     * Получает процент для стейкинга на основе тира и количества дней
     */
    public function getStakingPercentage(int $tierId, int $days): ?float
    {
        $tier = Tier::with('percentages')->find($tierId);

        if (!$tier) {
            return null;
        }

        $percentage = $tier->percentages()->where('days', $days)->first();

        return $percentage ? (float) $percentage->percentage : null;
    }
}
