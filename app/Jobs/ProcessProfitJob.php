<?php

namespace App\Jobs;

use App\Models\StakingDeposit;
use App\Models\ToastMessage;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * ProcessProfitJob - Ежедневное начисление профита по активным стейкингам
 *
 * Назначение:
 * Этот Job отвечает за ежедневное начисление процентов (profit) пользователям,
 * имеющим активные стейкинг-депозиты. Это промежуточные начисления до завершения стейкинга.
 *
 * ВАЖНО: Этот Job в текущей реализации НЕ используется активно.
 * В коде присутствует, но реальное начисление профита происходит только
 * в ProcessStakingCompletionJob при завершении всего срока стейкинга.
 *
 * Основные функции (если бы использовался):
 * 1. Начисляет ежедневный профит на balance пользователя
 * 2. Увеличивает earned_profit в записи стейкинга
 * 3. Создает транзакцию типа 'profit'
 * 4. Отправляет toast-уведомление о начислении
 *
 * Расчет ежедневного профита:
 * - Годовой процент (APR) делится на 365 дней
 * - Ежедневный профит = (тело депозита × APR% / 100) / 365
 * - Пример: депозит 1000 USDT @ 10% APR → ~0.27 USDT в день
 *
 * Входные параметры:
 * @param int $stakingDepositId - ID стейкинг-депозита
 * @param float $profitAmount - Сумма профита для начисления
 *
 * Вызывается из:
 * - Console\Commands\ProcessDailyProfits - cron команда (если реализована)
 * - В текущей версии проекта ежедневные начисления НЕ используются
 *
 * Текущая бизнес-логика проекта:
 * Профит начисляется единоразово при завершении стейкинга в ProcessStakingCompletionJob.
 * Этот Job сохранен для возможного будущего использования ежедневных начислений.
 *
 * Если потребуется включить ежедневные начисления:
 * 1. Создать команду ProcessDailyProfits для запуска по cron
 * 2. Команда должна перебирать активные стейкинги
 * 3. Рассчитывать ежедневный профит для каждого
 * 4. Запускать ProcessProfitJob с рассчитанной суммой
 * 5. Скорректировать ProcessStakingCompletionJob (чтобы не начислять профит дважды)
 *
 * Транзакционность:
 * Все операции выполняются в рамках одной DB транзакции с pessimistic locking (lockForUpdate)
 * для предотвращения race conditions.
 *
 * Обработка ошибок:
 * При неудаче логирует детали ошибки и отправляет пользователю toast-уведомление о проблеме.
 */
class ProcessProfitJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $stakingDepositId,
        public float $profitAmount
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            $stakingDeposit = StakingDeposit::lockForUpdate()->findOrFail($this->stakingDepositId);
            $user = User::lockForUpdate()->findOrFail($stakingDeposit->user_id);

            // Начисляем профит на баланс
            $user->balance += $this->profitAmount;
            $user->save();

            // Проверяем активацию пользователя после увеличения баланса
            if (!$user->active) {
                app(\App\Services\ReferralService::class)->activateUser($user);
            }

            // Обновляем earned_profit в стейкинге
            $stakingDeposit->earned_profit += $this->profitAmount;
            $stakingDeposit->save();

            // Создаем транзакцию
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'profit',
                'amount' => $this->profitAmount,
                'status' => 'confirmed',
                'details' => json_encode([
                    'staking_deposit_id' => $stakingDeposit->id,
                ]),
            ]);

            // Создаем успешное уведомление
            ToastMessage::createForUser(
                $user->id,
                'success',
                "Earned profit {$this->profitAmount} USDT from staking"
            );
        });
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        Log::error('ProcessProfitJob failed', [
            'staking_deposit_id' => $this->stakingDepositId,
            'profit_amount' => $this->profitAmount,
            'exception' => $exception?->getMessage(),
            'trace' => $exception?->getTraceAsString(),
        ]);

        $stakingDeposit = StakingDeposit::find($this->stakingDepositId);
        if ($stakingDeposit) {
            ToastMessage::createForUser(
                $stakingDeposit->user_id,
                'error',
                'Failed to process profit. Please contact support.'
            );
        }
    }
}
