<?php

namespace App\Jobs;

use App\Mail\DepositReplenishedMail;
use Illuminate\Support\Facades\Mail;

use App\Models\CryptoTransaction;
use App\Models\ToastMessage;
use App\Models\Transaction;
use App\Models\User;
use App\Services\ReferralService;
use App\Services\TelegramBotService;
use App\Services\TierService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * ProcessDepositJob - Обработка пополнения баланса пользователя через криптовалюту
 *
 * Назначение:
 * Этот Job отвечает за асинхронную обработку входящих депозитов USDT от пользователей.
 * Запускается автоматически при получении webhook от CryptocurrencyAPI.net о подтверждении транзакции.
 *
 * Основные функции:
 * 1. Увеличивает balance пользователя на сумму депозита
 * 2. Активирует пользователя при первом пополнении (для участия в реферальной программе)
 * 3. Пересчитывает tier (уровень) пользователя на основе нового баланса
 * 4. Создает запись транзакции в БД со статусом 'confirmed'
 * 5. Отправляет toast-уведомление пользователю об успешном пополнении
 *
 * Входные параметры:
 * @param int $userId - ID пользователя
 * @param float $amount - Сумма депозита в USDT
 * @param string $txHash - Hash транзакции в блокчейне
 * @param string $network - Сеть (по умолчанию 'tron')
 * @param string $token - Токен (по умолчанию 'USDT')
 *
 * Вызывается из:
 * - Api\WebhookController::cryptoDeposit() - при получении webhook от crypto API
 * - Cabinet\DepositController::testDeposit() - тестовый эндпоинт (только для разработки)
 *
 * Транзакционность:
 * Все операции выполняются в рамках одной DB транзакции с pessimistic locking (lockForUpdate)
 * для предотвращения race conditions при одновременных операциях с балансом.
 *
 * Обработка ошибок:
 * При неудаче логирует детали ошибки и отправляет пользователю toast-уведомление о проблеме.
 */
class ProcessDepositJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $userId,
        public float $amount,
        public string $txHash,
        public string $network = 'tron',
        public string $token = 'USDT'
    ) {
    }

    public function handle(ReferralService $referralService, TierService $tierService, TelegramBotService $telegramBotService): void
    {
        DB::transaction(function () use ($referralService, $tierService, $telegramBotService) {
            $user = User::lockForUpdate()->findOrFail($this->userId);

            // Увеличиваем balance
            $user->balance += $this->amount;
            $user->save();

            // Активируем пользователя при первом пополнении
            if (!$user->active) {
                $referralService->activateUser($user);
            }

            // Пересчитываем тир
            $tierService->recalculateUserTier($user);

            // Обновляем/создаем транзакцию: если была pending по tx_hash — переводим в confirmed
            $transaction = Transaction::updateOrCreate(
                ['tx_hash' => $this->txHash],
                [
                    'user_id' => $this->userId,
                    'type' => 'deposit',
                    'amount' => $this->amount,
                    'status' => 'confirmed',
                    'description' => "Deposit of {$this->amount} {$this->token}",
                    'tx_hash' => $this->txHash,
                    'meta' => [
                        'network' => $this->network,
                        'token' => $this->token,
                    ],
                ]
            );

            // Обновляем флаг processed в crypto_transactions
            $cryptoTransaction = CryptoTransaction::where('tx_hash', $this->txHash)->first();
            if ($cryptoTransaction) {
                $cryptoTransaction->update(['processed' => true]);
                $telegramBotService->sendDepositConfirmed($transaction, $cryptoTransaction);
            }

            // Создаем успешное уведомление
            ToastMessage::createForUser(
                $this->userId,
                'success',
                "Successfully deposited {$this->amount} {$this->token}",
                route('cabinet.dashboard')
            );

            // Email пользователю о пополнении
            try {
                Mail::mailer('failover')->to($user->email)->send(new DepositReplenishedMail(
                    $user->name,
                    $this->amount,
                    $this->token,
                    $this->network
                ));
            } catch (\Throwable $e) {
                Log::warning('Failed to send deposit replenished email', [
                    'user_id' => $this->userId,
                    'error' => $e->getMessage(),
                ]);
            }
        });
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('ProcessDepositJob failed', [
            'user_id' => $this->userId,
            'amount' => $this->amount,
            'tx_hash' => $this->txHash,
            'exception' => $exception?->getMessage(),
            'trace' => $exception?->getTraceAsString(),
        ]);

        ToastMessage::createForUser(
            $this->userId,
            'error',
            'Failed to process deposit. Please contact support.'
        );
    }
}
