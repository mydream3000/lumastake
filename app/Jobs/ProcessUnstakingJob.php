<?php

namespace App\Jobs;

use App\Models\StakingDeposit;
use App\Models\ToastMessage;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TierService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * ProcessUnstakingJob - Досрочное закрытие стейкинг-депозита со штрафом
 *
 * Назначение:
 * Этот Job обрабатывает запросы пользователей на досрочное закрытие (unstake) активных
 * стейкинг-депозитов до окончания срока с применением штрафа 10%.
 *
 * Основные функции:
 * 1. Проверяет, что стейкинг находится в статусе 'active'
 * 2. Применяет штраф 10% от суммы депозита
 * 3. Возвращает тело депозита за вычетом штрафа на баланс
 * 4. Изменяет статус стейкинга на 'cancelled'
 * 5. Записывает штраф как отрицательный earned_profit
 * 6. Пересчитывает tier пользователя (активный стейкинг закрыт)
 * 7. Создает транзакцию с деталями штрафа
 *
 * Расчет штрафа:
 * - Штраф = тело депозита × 10% (0.10)
 * - Возврат = тело депозита - штраф
 * - Пример: депозит 1000 USDT → штраф 100 USDT → возврат 900 USDT
 *
 * Входные параметры:
 * @param int $stakingDepositId - ID стейкинг-депозита для досрочного закрытия
 *
 * Вызывается из:
 * - Cabinet\StakingController::unstake() - когда пользователь нажимает кнопку "Unstake"
 *
 * Важные моменты:
 * - Весь накопленный к моменту unstake профит (earned_profit) теряется
 * - Стейкинг получает статус 'cancelled', а не 'completed'
 * - Tier пересчитывается, т.к. активный стейкинг закрыт (может понизиться уровень)
 * - earned_profit устанавливается в отрицательное значение (сумма штрафа)
 *
 * Отличия от ProcessStakingCompletionJob:
 * - Completion: возврат 100% + профит
 * - Unstaking: возврат 90%, без профита
 *
 * Транзакционность:
 * Все операции выполняются в рамках одной DB транзакции с pessimistic locking (lockForUpdate)
 * для предотвращения race conditions.
 *
 * Обработка ошибок:
 * При неудаче логирует детали ошибки и отправляет пользователю toast-уведомление о проблеме.
 */
class ProcessUnstakingJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $stakingDepositId
    ) {
    }

    public function handle(TierService $tierService): void
    {
        DB::transaction(function () use ($tierService) {
            $stakingDeposit = StakingDeposit::lockForUpdate()->findOrFail($this->stakingDepositId);

            if ($stakingDeposit->status !== 'active') {
                Log::warning('Staking deposit is not active', ['deposit'=>$stakingDeposit]);
                return;
            }

            $user = User::lockForUpdate()->findOrFail($stakingDeposit->user_id);

            // Досрочное закрытие стейкинга со штрафом 10%
            $penalty = $stakingDeposit->amount * 0.10;
            $amountToReturn = $stakingDeposit->amount - $penalty;

            // Возвращаем тело депозита за вычетом штрафа на баланс
            $user->balance += $amountToReturn;
            $user->save();

            // Проверяем активацию пользователя после увеличения баланса
            if (!$user->active) {
                app(\App\Services\ReferralService::class)->activateUser($user);
            }

            // Обновляем статус стейкинга
            $stakingDeposit->status = 'cancelled';
            $stakingDeposit->earned_profit = -$penalty; // Отрицательный профит из-за штрафа
            $stakingDeposit->save();

            // Пересчитываем тир (т.к. больше нет активного стейкинга)
            $tierService->recalculateUserTier($user);

            // Пересчитываем реферальный уровень у реферера (реферал выбыл из активных)
            try {
                if ($user->referred_by && ($referrer = User::find($user->referred_by))) {
                    app(\App\Services\ReferralService::class)->recalculateReferralLevel($referrer);
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to recalculate referral level on unstake', [
                    'referrer_id' => $user->referred_by,
                    'error' => $e->getMessage(),
                ]);
            }

            // Создаем транзакцию
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'staking',
                'amount' => $amountToReturn,
                'status' => 'confirmed',
                'description' => 'Early unstaking with 10% penalty',
                'meta' => [
                    'action' => 'unstake',
                    'staking_deposit_id' => $stakingDeposit->id,
                    'original_amount' => $stakingDeposit->amount,
                    'penalty' => $penalty,
                    'penalty_percentage' => 10,
                ],
            ]);

            // Создаем успешное уведомление с редиректом на stake history
            ToastMessage::createForUser(
                $user->id,
                'success',
                "Successfully unstaked {$amountToReturn} USDT (10% early withdrawal penalty: {$penalty} USDT)",
                route('cabinet.history')
            );
        });
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('ProcessUnstakingJob failed', [
            'staking_deposit_id' => $this->stakingDepositId,
            'exception' => $exception?->getMessage(),
            'trace' => $exception?->getTraceAsString(),
        ]);

        $stakingDeposit = StakingDeposit::find($this->stakingDepositId);
        if ($stakingDeposit) {
            ToastMessage::createForUser(
                $stakingDeposit->user_id,
                'error',
                'Failed to process unstaking request. Please try again or contact support.'
            );
        }
    }
}
