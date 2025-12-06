<?php

namespace App\Observers;

use App\Models\StakingDeposit;
use App\Models\Tier;

class StakingDepositObserver
{
    /**
     * Handle the StakingDeposit "created" event.
     */
    public function created(StakingDeposit $stakingDeposit): void
    {
        $this->updateUserTier($stakingDeposit);
    }

    /**
     * Handle the StakingDeposit "updating" event.
     */
    public function updating(StakingDeposit $stakingDeposit): void
    {
        // Автоматически рассчитываем earned_profit при изменении статуса на completed
        if ($stakingDeposit->isDirty('status') && $stakingDeposit->status === 'completed') {
            if ($stakingDeposit->earned_profit === null || $stakingDeposit->earned_profit == 0) {
                $stakingDeposit->earned_profit = round($stakingDeposit->amount * $stakingDeposit->percentage / 100, 2);
            }
        }
    }

    /**
     * Handle the StakingDeposit "updated" event.
     */
    public function updated(StakingDeposit $stakingDeposit): void
    {
        if ($stakingDeposit->wasChanged('status')) {
            $this->updateUserTier($stakingDeposit);
        }
    }

    /**
     * Update user's tier based on balance + stacked amount
     */
    private function updateUserTier(StakingDeposit $stakingDeposit): void
    {
        $user = $stakingDeposit->user;

        $stackedAmount = $user->stakingDeposits()
            ->whereIn('status', ['pending', 'active'])
            ->sum('amount');

        $totalBalance = $user->balance + $stackedAmount;

        $tier = Tier::query()
            ->where('is_active', true)
            ->where('min_balance', '<=', $totalBalance)
            ->orderByDesc('min_balance')
            ->first();

        if ($tier) {
            $user->current_tier = $tier->id;
        } else {
            // Если сумма ниже минимального порога первого tier — сбрасываем до "No Tier"
            $user->current_tier = null;
        }

        $user->saveQuietly();
    }
}
