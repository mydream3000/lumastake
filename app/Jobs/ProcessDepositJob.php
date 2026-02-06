<?php

namespace App\Jobs;

use App\Mail\TemplatedMail;
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
            // Проверка на дубликат: если транзакция с таким хешем уже подтверждена, выходим
            $alreadyConfirmed = Transaction::where('tx_hash', $this->txHash)
                ->where('user_id', $this->userId)
                ->where('status', 'confirmed')
                ->exists();

            if ($alreadyConfirmed) {
                Log::info('ProcessDepositJob: Transaction already confirmed, skipping balance increment.', [
                    'tx_hash' => $this->txHash,
                    'user_id' => $this->userId
                ]);
                return;
            }

            $user = User::lockForUpdate()->find($this->userId);

            if (!$user) {
                Log::error('ProcessDepositJob: User not found, skipping deposit.', [
                    'user_id' => $this->userId,
                    'tx_hash' => $this->txHash,
                    'amount' => $this->amount,
                ]);
                return;
            }

            // Увеличиваем balance
            $user->balance += $this->amount;
            $user->save();

            // Активируем пользователя при первом пополнении
            if (!$user->active) {
                $referralService->activateUser($user);
            }

            // Пересчитываем тир
            $tierService->recalculateUserTier($user);

            // Обновляем флаг processed в crypto_transactions
            $cryptoTransaction = CryptoTransaction::where('tx_hash', $this->txHash)
                ->where('user_id', $this->userId)
                ->first();

            // Если не нашли по user_id, ищем только по tx_hash (может быть другой user_id из-за бага)
            if (!$cryptoTransaction) {
                $cryptoTransaction = CryptoTransaction::where('tx_hash', $this->txHash)->first();
            }

            // Обновляем/создаем транзакцию: если была pending по tx_hash — переводим в confirmed
            $transaction = Transaction::updateOrCreate(
                [
                    'tx_hash' => $this->txHash,
                    'user_id' => $this->userId,
                ],
                [
                    'type' => 'deposit',
                    'amount' => $this->amount,
                    'status' => 'confirmed',
                    'is_real' => true,
                    'description' => "Deposit of {$this->amount} {$this->token}",
                    'tx_hash' => $this->txHash,
                    'wallet_address' => $cryptoTransaction?->address,
                    'network' => $this->network,
                    'meta' => [
                        'network' => $this->network,
                        'token' => $this->token,
                        'address' => $cryptoTransaction?->address,
                    ],
                ]
            );

            // Помечаем CryptoTransaction как обработанную и отправляем Telegram
            if ($cryptoTransaction) {
                $cryptoTransaction->update(['processed' => true]);
            }

            // Отправляем Telegram уведомление всегда (даже если CryptoTransaction не найдена)
            try {
                $telegramBotService->sendDepositConfirmed($transaction, $cryptoTransaction);
            } catch (\Throwable $e) {
                Log::warning('Failed to send Telegram deposit notification', [
                    'tx_hash' => $this->txHash,
                    'error' => $e->getMessage(),
                ]);
            }

            // Создаем успешное уведомление
            ToastMessage::createForUser(
                $this->userId,
                'success',
                "Successfully deposited {$this->amount} {$this->token}",
                route('cabinet.dashboard')
            );

            // Email пользователю о пополнении (используем шаблон из БД)
            try {
                Mail::mailer('failover')->to($user->email)->send(new TemplatedMail(
                    'deposit_replenished',
                    [
                        'userName' => $user->name,
                        'amount' => number_format($this->amount, 2),
                        'token' => $this->token,
                        'network' => $this->network,
                    ],
                    $this->userId
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
