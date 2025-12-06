<?php

namespace App\Jobs;

use App\Models\InvestmentPoolIslamic;
use App\Models\StakingDeposit;
use App\Models\ToastMessage;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TierService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\TemplatedMail;
use Throwable;

/**
 * ProcessStakingJob - Создание нового стейкинг-депозита
 *
 * Назначение:
 * Этот Job отвечает за создание новых стейкинг-депозитов пользователей.
 * Переводит средства с баланса пользователя в активный стейкинг под процент.
 *
 * Основные функции:
 * 1. Проверяет минимальную сумму стейкинга (по умолчанию 50 USDT)
 * 2. Проверяет достаточность баланса
 * 3. Списывает средства с balance
 * 4. Создает новый StakingDeposit со статусом 'active'
 * 5. Пересчитывает tier пользователя (наличие активного стейкинга влияет на уровень)
 * 6. Создает транзакцию типа 'staking'
 * 7. Отправляет toast-уведомление о создании стейкинга
 *
 * Входные параметры:
 * @param int $userId - ID пользователя
 * @param int $tierId - ID tier (уровень доходности)
 * @param float $amount - Сумма стейкинга в USDT
 * @param int $days - Период стейкинга в днях (30, 60, 90, 120, 180, 365)
 * @param float $percentage - Процент доходности (APR)
 * @param bool $autoStake - Автоматическое продление стейкинга (по умолчанию false)
 *
 * Вызывается из:
 * - Cabinet\StakingController::store() - при создании нового стейкинга вручную
 * - ProcessStakingCompletionJob - при автоматическом продлении стейкинга (auto_renewal)
 *
 * Периоды стейкинга и расчет профита:
 * Процент начисляется ежедневно через ProcessProfitJob (запускается по cron).
 * В конце периода срабатывает ProcessStakingCompletionJob для завершения стейкинга.
 *
 * Влияние на tier:
 * Наличие активного стейкинга может повысить tier пользователя, что дает:
 * - Более высокие проценты по стейкингу
 * - Более высокие проценты по реферальной программе
 *
 * Транзакционность:
 * Все операции выполняются в рамках одной DB транзакции с pessimistic locking (lockForUpdate)
 * для предотвращения race conditions при одновременных операциях с балансом.
 *
 * Обработка ошибок:
 * При неудаче логирует детали ошибки и отправляет пользователю toast-уведомление о проблеме.
 */
class ProcessStakingJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $userId,
        public int $tierId,
        public float $amount,
        public int $days,
        public float $percentage,
        public bool $autoStake = false
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(TierService $tierService): void
    {
        DB::transaction(function () use ($tierService) {
            $user = User::lockForUpdate()->findOrFail($this->userId);

            // Проверяем минимальную сумму стейкинга
            $minStakingAmount = config('pools.min_staking_amount', 50);
            if ($this->amount < $minStakingAmount) {
                throw new \Exception("Minimum staking amount is {$minStakingAmount} USDT");
            }

            // Проверяем доступный баланс (исключая суммы, зарезервированные на вывод)
            if ($user->available_balance < $this->amount) {
                throw new \Exception('Insufficient balance');
            }

            $finalPercentage = $this->percentage;

            if ($user->account_type === 'islamic') {
                $islamicPool = InvestmentPoolIslamic::where('tier_id', $this->tierId)
                    ->where('duration_days', $this->days)
                    ->first();

                if ($islamicPool) {
                    // For Islamic accounts, profit is not known at the start.
                    // We set percentage to 0 to indicate "pending/variable" profit.
                    // The actual random profit will be calculated at completion.
                    $finalPercentage = 0;
                }
            }

            // Списываем средства с баланса
            $user->balance -= $this->amount;
            $user->save();

            // Создаем стейкинг
            $stakingDeposit = StakingDeposit::create([
                'user_id' => $this->userId,
                'tier_id' => $this->tierId,
                'amount' => $this->amount,
                'days' => $this->days,
                'percentage' => $finalPercentage,
                'earned_profit' => 0,
                'start_date' => now(),
                'end_date' => now()->addDays($this->days),
                'status' => 'active',
                'auto_renewal' => $this->autoStake,
            ]);

            // Пересчитываем тир пользователя (т.к. теперь есть активный staking)
            $tierService->recalculateUserTier($user);

            // Создаем транзакцию
            Transaction::create([
                'user_id' => $this->userId,
                'type' => 'staking',
                'amount' => $this->amount,
                'status' => 'confirmed',
                'description' => "Staked {$this->amount} USDT for {$this->days} days",
                'meta' => [
                    'action' => 'stake',
                    'staking_deposit_id' => $stakingDeposit->id,
                    'days' => $this->days,
                    'percentage' => $finalPercentage,
                ],
            ]);

            // Создаем успешное уведомление с редиректом на staking
            ToastMessage::createForUser(
                $this->userId,
                'success',
                "Successfully staked {$this->amount} USDT for {$this->days} days",
                route('cabinet.staking.index')
            );

            // Email уведомления (EN)
            try {
                // Письмо стейкеру
                Mail::to($user->email)->send(new TemplatedMail(
                    'staking_created_notice',
                    [
                        'name' => $user->name,
                        'amount' => number_format($this->amount, 2),
                        'days' => $this->days,
                        'percentage' => $finalPercentage,
                    ],
                    $user->id,
                    'staking_deposit',
                    $stakingDeposit->id
                ));

                // Письмо рефереру (если есть)
                if ($user->referred_by) {
                    $referrer = User::find($user->referred_by);
                    if ($referrer) {
                        Mail::to($referrer->email)->send(new TemplatedMail(
                            'referral_staked_notice',
                            [
                                'referrer_name' => $referrer->name,
                                'referral_name' => $user->name,
                                'amount' => number_format($this->amount, 2),
                                'days' => $this->days,
                                'percentage' => $finalPercentage,
                            ],
                            $referrer->id,
                            'staking_deposit',
                            $stakingDeposit->id
                        ));

                        // Пересчитываем реферальный уровень реферера по активным партнёрам
                        try {
                            app(\App\Services\ReferralService::class)->recalculateReferralLevel($referrer);
                        } catch (\Throwable $e) {
                            Log::warning('Failed to recalculate referral level on staking created', [
                                'referrer_id' => $referrer->id,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to send staking created/referral notice emails', [
                    'user_id' => $this->userId,
                    'staking_deposit_id' => $stakingDeposit->id,
                    'error' => $e->getMessage(),
                ]);
            }
        });
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        Log::error('ProcessStakingJob failed', [
            'user_id' => $this->userId,
            'amount' => $this->amount,
            'days' => $this->days,
            'exception' => $exception?->getMessage(),
            'trace' => $exception?->getTraceAsString(),
        ]);

        ToastMessage::createForUser(
            $this->userId,
            'error',
            'Failed to process staking request. Please try again or contact support.'
        );
    }
}
