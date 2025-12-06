<?php

namespace App\Jobs;

use App\Mail\StakingCompletedMail;
use App\Mail\TemplatedMail;
use App\Models\InvestmentPoolIslamic;
use App\Models\StakingDeposit;
use App\Models\ToastMessage;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * ProcessStakingCompletionJob - Завершение стейкинг-депозита по окончании срока
 *
 * Назначение:
 * Этот Job автоматически завершает стейкинг-депозиты, когда истекает срок их действия.
 * Возвращает основную сумму на баланс, начисляет профит и обрабатывает автопродление.
 *
 * Основные функции:
 * 1. Проверяет, что стейкинг активен и срок завершен
 * 2. Рассчитывает финальный профит (amount * percentage / 100)
 * 3. Возвращает тело депозита (principal) на баланс
 * 4. Начисляет профит на баланс
 * 5. Создает транзакцию возврата тела
 * 6. Создает earning запись для профита
 * 7. Начисляет реферальную награду рефереру от профита
 * 8. Обрабатывает автоматическое продление (auto_renewal)
 *
 * Входные параметры:
 * @param int $stakingDepositId - ID стейкинг-депозита для завершения
 *
 * Вызывается из:
 * - Console\Commands\ProcessStakingCompletions - cron команда, проверяющая завершенные стейкинги
 * - Запускается автоматически по расписанию для каждого депозита, у которого end_date наступила
 *
 * Автоматическое продление (auto_renewal):
 * Если включено auto_renewal:
 * - Создается новый стейкинг ТОЛЬКО на сумму тела (без профита)
 * - Профит добавляется на баланс
 * - Уведомление сообщает о продлении
 * - Tier НЕ пересчитывается (т.к. стейкинг продолжается)
 *
 * Если auto_renewal выключено:
 * - Стейкинг завершается со статусом 'completed'
 * - И тело, и профит возвращаются на balance
 * - Пересчитывается tier пользователя
 *
 * Реферальная программа:
 * При завершении стейкинга рефереру начисляется награда от профита стейкинга
 * через ReferralService::processReferralRewardFromProfit()
 *
 * Транзакционность:
 * Все операции выполняются в рамках одной DB транзакции с pessimistic locking (lockForUpdate)
 * для предотвращения race conditions.
 *
 * Обработка ошибок:
 * При неудаче логирует детали ошибки и отправляет пользователю toast-уведомление о проблеме.
 */
class ProcessStakingCompletionJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $stakingDepositId
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(\App\Services\TierService $tierService, \App\Services\ReferralService $referralService): void
    {
        DB::transaction(function () use ($tierService, $referralService) {
            $stakingDeposit = StakingDeposit::lockForUpdate()->findOrFail($this->stakingDepositId);

            if ($stakingDeposit->status !== 'active') {
                Log::warning('Staking deposit is not active', ['deposit'=>$stakingDeposit]);
                return;
            }

            if ($stakingDeposit->end_date->isFuture()) {
                Log::warning('Staking period has not ended yet', ['deposit'=>$stakingDeposit]);
                return;
            }

            $user = User::lockForUpdate()->findOrFail($stakingDeposit->user_id);

            // If Islamic account (percentage is 0), calculate random profit now
            if ($stakingDeposit->percentage == 0 || $user->account_type === 'islamic') {
                $islamicPool = InvestmentPoolIslamic::where('tier_id', $stakingDeposit->tier_id)
                    ->where('duration_days', $stakingDeposit->days)
                    ->first();

                if ($islamicPool) {
                    $min = $islamicPool->min_percentage;
                    $max = $islamicPool->max_percentage;
                    // Generate random float with 1 decimal place
                    $randomFloat = mt_rand((int)($min * 10), (int)($max * 10)) / 10.0;

                    $stakingDeposit->percentage = $randomFloat;
                    $stakingDeposit->save();
                }
            }

            // Рассчитываем профит
            $profit = round($stakingDeposit->amount * $stakingDeposit->percentage / 100, 2);

            // Возвращаем тело депозита на баланс
            $user->balance += $stakingDeposit->amount;

            // Добавляем профит на баланс
            $user->balance += $profit;

            $user->save();

            // Проверяем активацию пользователя после увеличения баланса
            if (!$user->active) {
                $referralService->activateUser($user);
            }

            // Обновляем стейкинг
            $stakingDeposit->earned_profit = $profit;
            $stakingDeposit->status = 'completed';
            $stakingDeposit->save();

            // Создаем транзакцию возврата тела
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'staking',
                'amount' => $stakingDeposit->amount,
                'status' => 'confirmed',
                'description' => 'Staking completed - principal returned',
                'meta' => [
                    'action' => 'completion',
                    'staking_deposit_id' => $stakingDeposit->id,
                ],
            ]);

            // Создаем запись в earnings для профита
            \App\Models\Earning::create([
                'user_id' => $user->id,
                'type' => 'profit',
                'amount' => $profit,
                'description' => "Staking profit: {$stakingDeposit->days} days @ {$stakingDeposit->percentage}%",
                'meta' => [
                    'staking_deposit_id' => $stakingDeposit->id,
                    'principal_amount' => $stakingDeposit->amount,
                    'percentage' => $stakingDeposit->percentage,
                    'days' => $stakingDeposit->days,
                    'tier_name' => $stakingDeposit->tier->name,
                ],
            ]);

            // Начисляем реферальную награду рефереру от профита стейкинга
            if ($user->referred_by) {
                $referralService->processReferralRewardFromProfit($user, $profit, $stakingDeposit->id, $stakingDeposit->days);
                // После завершения стейка у реферала пересчитаем уровень реферера (активные партнёры могли измениться)
                try {
                    if ($referrer = \App\Models\User::find($user->referred_by)) {
                        app(\App\Services\ReferralService::class)->recalculateReferralLevel($referrer);
                    }
                } catch (\Throwable $e) {
                    \Log::warning('Failed to recalculate referral level on staking completion', [
                        'referrer_id' => $user->referred_by,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Создаем уведомление в зависимости от auto-renewal
            if ($stakingDeposit->auto_renewal) {
                ToastMessage::createForUser(
                    $user->id,
                    'success',
                    "Staking completed! Principal {$stakingDeposit->amount} USDT auto-renewed for {$stakingDeposit->days} days @ {$stakingDeposit->percentage}%. Profit {$profit} USDT is now available for withdrawal.",
                    route('cabinet.staking.index')
                );

                // Отправляем email уведомление
                Mail::to($user->email)->send(new TemplatedMail(
                    'staking_completed',
                    [
                        'userName' => $user->name,
                        'principalAmount' => $stakingDeposit->amount,
                        'profitAmount' => $profit,
                        'totalAmount' => $stakingDeposit->amount + $profit,
                        'days' => $stakingDeposit->days,
                        'percentage' => $stakingDeposit->percentage,
                        'autoRenewal' => true,
                    ],
                    $user->id,
                    'staking_deposit',
                    $stakingDeposit->id
                ));

                // Создаем новый стейкинг ТОЛЬКО на сумму тела
                ProcessStakingJob::dispatch(
                    $user->id,
                    $stakingDeposit->tier_id,
                    $stakingDeposit->amount, // ✅ Только тело, без профита
                    $stakingDeposit->days,
                    $stakingDeposit->percentage,
                    true // auto_renewal включен
                );
            } else {
                ToastMessage::createForUser(
                    $user->id,
                    'success',
                    "Staking completed! Returned {$stakingDeposit->amount} USDT (principal) + {$profit} USDT (profit). Total: " . ($stakingDeposit->amount + $profit) . " USDT now available.",
                    route('cabinet.history')
                );

                // Отправляем email уведомление
                Mail::to($user->email)->send(new TemplatedMail(
                    'staking_completed',
                    [
                        'userName' => $user->name,
                        'principalAmount' => $stakingDeposit->amount,
                        'profitAmount' => $profit,
                        'totalAmount' => $stakingDeposit->amount + $profit,
                        'days' => $stakingDeposit->days,
                        'percentage' => $stakingDeposit->percentage,
                        'autoRenewal' => false,
                    ],
                    $user->id,
                    'staking_deposit',
                    $stakingDeposit->id
                ));

                // Пересчитываем тир только если нет auto-renewal
                $tierService->recalculateUserTier($user);
            }
        });
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        Log::error('ProcessStakingCompletionJob failed', [
            'staking_deposit_id' => $this->stakingDepositId,
            'exception' => $exception?->getMessage(),
            'trace' => $exception?->getTraceAsString(),
        ]);

        $stakingDeposit = StakingDeposit::find($this->stakingDepositId);
        if ($stakingDeposit) {
            ToastMessage::createForUser(
                $stakingDeposit->user_id,
                'error',
                'Failed to complete staking. Please contact support.'
            );
        }
    }
}

