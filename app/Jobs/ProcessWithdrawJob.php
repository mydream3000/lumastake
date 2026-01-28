<?php

namespace App\Jobs;

use App\Mail\TemplatedMail;
use App\Models\ToastMessage;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TelegramBotService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * ProcessWithdrawJob - Обработка запроса на вывод средств пользователем
 *
 * Назначение:
 * Этот Job отвечает за асинхронную обработку запросов пользователей на вывод USDT.
 * Создает pending-транзакцию и резервирует средства на балансе пользователя.
 *
 * Основные функции:
 * 1. Проверяет доступный баланс (balance)
 * 2. Создает транзакцию со статусом 'pending'
 * 3. Отправляет toast-уведомление об успешном создании заявки на вывод
 *
 * Особенности балансов:
 * - При создании заявки баланс НЕ списывается
 * - Баланс списывается только при approve админом
 * - Если недостаточно баланса, Job завершается с ошибкой
 *
 * Входные параметры:
 * @param int $userId - ID пользователя
 * @param float $amount - Сумма вывода в USDT
 * @param string $receiverAddress - Адрес кошелька получателя (Tron network)
 *
 * Вызывается из:
 * - Cabinet\WithdrawController::store() - при создании заявки на вывод
 *
 * Статусы транзакции:
 * - 'pending' - заявка создана, ожидает обработки администратором
 * - В дальнейшем админ может изменить статус на 'confirmed' или 'cancelled'
 *
 * Важно:
 * Этот Job НЕ отправляет реальные средства в блокчейн. Он только резервирует баланс
 * и создает заявку. Фактическая отправка USDT происходит вручную администратором
 * или через отдельный процесс обработки pending-выводов.
 *
 * Транзакционность:
 * Все операции выполняются в рамках одной DB транзакции с pessimistic locking (lockForUpdate)
 * для предотвращения race conditions при одновременных операциях с балансом.
 *
 * Обработка ошибок:
 * При неудаче логирует детали ошибки и отправляет пользователю toast-уведомление о проблеме.
 */
class ProcessWithdrawJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $userId,
        public float $amount,
        public string $receiverAddress,
        public ?string $network = 'tron',
        public string $token = 'USDT'
    ) {
    }

    public function handle(TelegramBotService $telegramBotService): void
    {
        DB::transaction(function () use ($telegramBotService) {
            $user = User::lockForUpdate()->findOrFail($this->userId);

            // Проверяем доступный баланс
            if ($user->balance < $this->amount) {
                throw new \Exception('Insufficient balance');
            }

            // НЕ списываем баланс сразу - списание произойдет при approve админом
            // Баланс остается на счету до момента подтверждения заявки

            // Создаем транзакцию вывода со статусом pending
            $transaction = Transaction::create([
                'user_id' => $this->userId,
                'type' => 'withdraw',
                'amount' => $this->amount,
                'status' => 'pending',
                'description' => "Withdrawal of {$this->amount} {$this->token}",
                'wallet_address' => $this->receiverAddress,
                'network' => $this->network ?? 'tron',
                'meta' => [
                    'token' => $this->token,
                    'receiver_address' => $this->receiverAddress,
                    'network' => $this->network ?? 'tron',
                ],
            ]);

            // Отправляем уведомление в Telegram о создании заявки на вывод
            $telegramBotService->sendWithdrawCreated($transaction);

            // Отправляем email пользователю о создании заявки на вывод (используем шаблон из БД)
            try {
                Mail::mailer('failover')
                    ->to($user->email)
                    ->send(new TemplatedMail(
                        'withdrawal_created',
                        [
                            'userName' => $user->name,
                            'amount' => number_format($this->amount, 2),
                            'walletAddress' => $this->receiverAddress,
                            'token' => $this->token,
                            'network' => $this->network ?? 'tron',
                        ],
                        $this->userId
                    ));
            } catch (\Throwable $e) {
                Log::error('Failed to send withdrawal created email', [
                    'user_id' => $this->userId,
                    'transaction_id' => $transaction->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                ]);
            }

            // Создаем успешное уведомление
            ToastMessage::createForUser(
                $this->userId,
                'success',
                "Withdrawal request for {$this->amount} {$this->token} has been submitted successfully"
            );
        });
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        Log::error('ProcessWithdrawJob failed', [
            'user_id' => $this->userId,
            'amount' => $this->amount,
            'receiver_address' => $this->receiverAddress,
            'exception' => $exception?->getMessage(),
            'trace' => $exception?->getTraceAsString(),
        ]);

        ToastMessage::createForUser(
            $this->userId,
            'error',
            'Failed to process withdrawal request. Please try again or contact support.'
        );
    }
}
