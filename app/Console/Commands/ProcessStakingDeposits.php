<?php

namespace App\Console\Commands;

use App\Models\StakingDeposit;
use App\Models\ToastMessage;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TierService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Команда обработки завершенных стейкинг-депозитов с автопродлением
 *
 * ВНИМАНИЕ: Эта команда дублирует функционал ProcessCompletedStakings и ProcessStakingCompletionJob.
 * Обрабатывает завершенные стейкинги синхронно, в рамках одной транзакции.
 *
 * Основная бизнес-логика:
 * 1. Находит активные стейкинги с end_date <= now
 * 2. Начисляет профит и возвращает тело депозита на баланс
 * 3. При auto_renewal = true создает новый стейкинг на тех же условиях
 * 4. При auto_renewal = false пересчитывает тир пользователя
 * 5. Создает транзакцию типа 'profit' для истории операций
 * 6. Отправляет toast-уведомление пользователю
 *
 * ВАЖНЫЕ МОМЕНТЫ:
 * - При автопродлении тир НЕ пересчитывается (активный стейкинг продолжается)
 * - Используются транзакции БД для атомарности операций
 * - lockForUpdate() предотвращает race conditions
 *
 * Рекомендуется использовать ProcessCompletedStakings + очередь для масштабируемости.
 */
class ProcessStakingDeposits extends Command
{
    protected $signature = 'staking:process';
    protected $description = 'Process completed staking deposits and handle auto-renewal';

    public function handle(TierService $tierService): int
    {
        $this->info('Processing completed staking deposits...');

        // Получаем все активные стейки, у которых наступила end_date
        $completedStakes = StakingDeposit::where('status', 'active')
            ->where('end_date', '<=', now())
            ->get();

        $processedCount = 0;

        foreach ($completedStakes as $stake) {
            try {
                DB::transaction(function () use ($stake, $tierService) {
                    $user = User::lockForUpdate()->findOrFail($stake->user_id);

                    // Рассчитываем профит
                    $profit = ($stake->amount * $stake->percentage) / 100;
                    $totalReturn = $stake->amount + $profit;

                    // Возвращаем на баланс: тело + профит
                    $user->balance += $totalReturn;
                    $user->save();

                    // Проверяем активацию пользователя после увеличения баланса
                    if (!$user->active) {
                        app(\App\Services\ReferralService::class)->activateUser($user);
                    }

                    // Обновляем стейк
                    $stake->status = 'completed';
                    $stake->earned_profit = $profit;
                    $stake->save();

                    // Если включен auto-renewal, создаем новый стейк
                    if ($stake->auto_renewal) {
                        // Списываем с баланса для нового стейка
                        $user->balance -= $stake->amount;
                        $user->save();

                        StakingDeposit::create([
                            'user_id' => $stake->user_id,
                            'tier_id' => $stake->tier_id,
                            'amount' => $stake->amount,
                            'days' => $stake->days,
                            'percentage' => $stake->percentage,
                            'earned_profit' => 0,
                            'start_date' => now(),
                            'end_date' => now()->addDays($stake->days),
                            'status' => 'active',
                            'auto_renewal' => true,
                        ]);
                    } else {
                        // Пересчитываем тир (если не auto-renewal, т.к. больше нет активного стейка)
                        $tierService->recalculateUserTier($user);
                    }

                    // Создаем транзакцию профита
                    Transaction::create([
                        'user_id' => $stake->user_id,
                        'type' => 'profit',
                        'amount' => $profit,
                        'status' => 'confirmed',
                        'description' => "Staking profit from {$stake->days} days stake",
                        'meta' => [
                            'staking_deposit_id' => $stake->id,
                            'stake_amount' => $stake->amount,
                            'percentage' => $stake->percentage,
                            'days' => $stake->days,
                            'auto_renewal' => $stake->auto_renewal,
                        ],
                    ]);

                    // Уведомляем пользователя
                    $message = $stake->auto_renewal
                        ? "Your staking has completed! Profit: {$profit} USDT. Auto-renewal activated for another {$stake->days} days."
                        : "Your staking has completed! Total return: {$totalReturn} USDT (including {$profit} USDT profit).";

                    ToastMessage::createForUser(
                        $stake->user_id,
                        'success',
                        $message,
                        route('cabinet.earnings.profit')
                    );
                });

                $processedCount++;
                $this->info("Processed stake #{$stake->id}");
            } catch (\Exception $e) {
                $this->error("Failed to process stake #{$stake->id}: " . $e->getMessage());
            }
        }

        $this->info("Processed {$processedCount} completed stakes");

        return self::SUCCESS;
    }
}
