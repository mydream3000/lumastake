<?php

namespace App\Jobs;

use App\Models\ToastMessage;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * ProcessWithdrawCancellationJob - Отмена заявки на вывод средств
 *
 * Назначение:
 * Этот Job обрабатывает отмену pending-заявок на вывод средств.
 * Возвращает зарезервированные средства обратно на баланс пользователя.
 *
 * Основные функции:
 * 1. Проверяет, что транзакция является pending-выводом
 * 2. Возвращает сумму обратно на баланс пользователя
 * 3. Изменяет статус транзакции на 'cancelled'
 * 4. Добавляет метаданные об отмене (кем и когда)
 * 5. Отправляет toast-уведомление о возврате средств
 * 6. Логирует информацию об отмене
 *
 * Особенности балансов:
 * - При создании заявки баланс НЕ списывался, но был зарезервирован
 * - При отмене заявки баланс возвращается обратно
 * - Пользователь снова может использовать эти средства
 *
 * Входные параметры:
 * @param int $transactionId - ID транзакции вывода для отмены
 * @param string $reason - Причина отмены (по умолчанию 'Cancelled by user')
 *
 * Вызывается из:
 * - Cabinet\WithdrawController::cancel() - когда пользователь отменяет свою заявку
 * - Admin\PaymentController::cancelWithdraw() - когда админ отклоняет заявку
 *
 * Возможные причины отмены:
 * - 'Cancelled by user' - отменил сам пользователь
 * - 'Cancelled by admin' - отклонил администратор
 * - 'Insufficient funds in hot wallet' - недостаточно средств для обработки
 * - 'Invalid withdrawal address' - некорректный адрес получателя
 *
 * Статусы транзакции:
 * - До: 'pending' (ожидает обработки)
 * - После: 'cancelled' (отменена)
 *
 * Важно:
 * Job может отменить только транзакции со статусом 'pending'.
 * Попытка отменить confirmed или уже cancelled транзакцию вызовет Exception.
 *
 * Метаданные:
 * В meta транзакции добавляются поля:
 * - 'cancelled_by' - кто отменил ('user' или 'admin')
 * - 'cancelled_at' - timestamp отмены
 * - 'cancellation_reason' - причина отмены (если указана)
 *
 * Транзакционность:
 * Все операции выполняются в рамках одной DB транзакции с pessimistic locking (lockForUpdate)
 * для предотвращения race conditions при одновременных операциях с балансом.
 *
 * Обработка ошибок:
 * При неудаче логирует детали ошибки. Toast-уведомление не отправляется при ошибке,
 * т.к. может не быть возможности определить user_id из несуществующей транзакции.
 */
class ProcessWithdrawCancellationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $transactionId,
        public string $reason = 'Cancelled by user'
    ) {
    }

    public function handle(): void
    {
        DB::transaction(function () {
            $transaction = Transaction::lockForUpdate()->findOrFail($this->transactionId);

            if ($transaction->type !== 'withdraw' || $transaction->status !== 'pending') {
                throw new \Exception('Can only cancel pending withdrawals');
            }

            $user = User::lockForUpdate()->findOrFail($transaction->user_id);

            // Баланс не изменяем — при создании заявки он не списывался

            // Обновляем статус транзакции
            $transaction->status = 'cancelled';
            $transaction->meta = array_merge($transaction->meta ?? [], [
                'cancelled_by' => 'user',
                'cancelled_at' => now()->toDateTimeString(),
                'cancellation_reason' => $this->reason,
            ]);
            $transaction->save();

            // Уведомляем пользователя
            ToastMessage::createForUser(
                $user->id,
                'success',
                "Withdrawal request for {$transaction->amount} USDT has been cancelled.",
                route('cabinet.transactions.withdraw')
            );

            Log::info('Withdrawal cancelled by user', [
                'transaction_id' => $this->transactionId,
                'user_id' => $user->id,
                'amount' => $transaction->amount,
                'reason' => $this->reason,
            ]);
        });
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('ProcessWithdrawCancellationJob failed', [
            'transaction_id' => $this->transactionId,
            'exception' => $exception?->getMessage(),
            'trace' => $exception?->getTraceAsString(),
        ]);
    }
}
