<?php

namespace App\Observers;

use App\Models\ReferralLevel;
use App\Models\User;
use App\Services\ReferralService;

class ReferralLevelObserver
{
    /**
     * Пересчитать реферальные уровни всех пользователей при изменении таблицы
     */
    public function updated(ReferralLevel $referralLevel): void
    {
        $this->recalculateAllUsers();
    }

    /**
     * Пересчитать при создании нового уровня
     */
    public function created(ReferralLevel $referralLevel): void
    {
        $this->recalculateAllUsers();
    }

    /**
     * Пересчитать при удалении уровня
     */
    public function deleted(ReferralLevel $referralLevel): void
    {
        $this->recalculateAllUsers();
    }

    /**
     * Пересчитать реферальные уровни для всех пользователей
     */
    protected function recalculateAllUsers(): void
    {
        $referralService = app(ReferralService::class);

        // Получаем всех пользователей с рефералами
        User::whereHas('referrals', function ($query) {
            $query->where('active', true);
        })->chunk(100, function ($users) use ($referralService) {
            foreach ($users as $user) {
                $referralService->recalculateReferralLevel($user);
            }
        });
    }
}
