<?php

namespace App\Observers;

use App\Models\Tier;
use App\Models\User;
use Illuminate\Support\Str;

class UserObserver
{
    /**
     * Handle the User "creating" event.
     */
    public function creating(User $user): void
    {
        if (empty($user->uuid)) {
            $user->uuid = (string) Str::uuid();
        }
    }

    /**
     * Handle the User "updating" event.
     */
    public function updating(User $user): void
    {
        if ($user->isDirty(['balance'])) {
            $this->updateTier($user);
        }
    }

    /**
     * Recalculate user's tier based on total balance + stacked amount
     */
    private function updateTier(User $user): void
    {
        // Stacked = sum of all active staking deposits
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
    }
}
