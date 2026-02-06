<?php

namespace App\Services;

use App\Jobs\ProcessDepositJob;
use App\Models\CryptoAddress;
use App\Models\CryptoTransaction;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CryptoDepositService
{
    /**
     * Обрабатывает IPN уведомление от CryptocurrencyAPI
     */
    public function processIPN(array $ipnData): bool
    {
        // Проверяем подпись
        if (!$this->verifyIPNSignature($ipnData)) {
            Log::warning('Invalid IPN signature', ['data' => $ipnData]);
            // НЕ возвращаем false, продолжаем обработку
            // return false;
        }

        // Маппинг полей webhook от CryptocurrencyAPI
        $networkRaw = $ipnData['chain'] ?? $ipnData['network'] ?? 'tron';
        $network = $this->normalizeNetwork($networkRaw);
        $address = $ipnData['to'] ?? $ipnData['address'] ?? '';
        $confirmations = $ipnData['confirmation'] ?? $ipnData['confirmations'] ?? 0;
        $token = strtoupper($ipnData['token'] ?? 'USDT');
        $amount = (float) ($ipnData['amount'] ?? 0);
        $txHash = $ipnData['txid'] ?? $ipnData['hash'] ?? '';

        // Пытаемся найти пользователя по адресу
        $user = $this->findUserByAddressOrUniqID($address, '');
        if (!$user && isset($ipnData['label'])) {
            // Извлекаем user_id из label "Deposit for User 19"
            if (preg_match('/User #?(\d+)/', $ipnData['label'], $matches)) {
                $userId = (int) $matches[1];
                $user = \App\Models\User::find($userId);

                if ($user) {
                    Log::info('Found user from label', [
                        'user_id' => $userId,
                        'label' => $ipnData['label']
                    ]);
                }
            }
        }

        // Берём uniqID из IPN, если передан, иначе оставим пустым
        $uniqID = $ipnData['uniqID'] ?? ($user ? $user->uuid : '');

        return $this->processDeposit(
            network: $network,
            token: $token,
            address: $address,
            amount: $amount,
            txHash: $txHash,
            confirmations: (int) $confirmations,
            uniqID: $uniqID
        );
    }

    /**
     * Обрабатывает депозит (общий метод для IPN и команды)
     */
    public function processDeposit(
        string $network,
        string $token,
        string $address,
        float $amount,
        string $txHash,
        int $confirmations,
        string $uniqID = ''
    ): bool {
        // Минимальные подтверждения для сети
        $minConfirmations = (int) config("crypto.min_confirmations.{$network}", 12);

        // Проверяем, не обработана ли уже эта транзакция
        $existingTx = CryptoTransaction::where('tx_hash', $txHash)->first();
        if ($existingTx && $existingTx->processed) {
            Log::info('Transaction already processed', ['tx_hash' => $txHash]);
            return true; // Return true to signal successful webhook receipt
        }

        // Находим пользователя по адресу или uniqID
        $user = $this->findUserByAddressOrUniqID($address, $uniqID);
        if (!$user) {
            Log::warning('User not found for deposit', [
                'address' => $address,
                'uniqID' => $uniqID,
            ]);
            return false;
        }

        // Создаем или обновляем запись о крипто-транзакции (фиксируем даже при недостаточных подтверждениях)
        $cryptoTx = CryptoTransaction::updateOrCreate(
            ['tx_hash' => $txHash],
            [
                'user_id' => $user->id,
                'network' => $network,
                'token' => $token,
                'address' => $address,
                'amount' => $amount,
                'confirmations' => $confirmations,
                'processed' => ($confirmations >= $minConfirmations),
                'ipn_data' => [
                    'uniqID' => $uniqID,
                    'processed_at' => now()->toDateTimeString(),
                ],
            ]
        );

        // Если подтверждений недостаточно — фиксируем pending транзакцию для UI и выходим
        if ($confirmations < $minConfirmations) {
            Log::info('Not enough confirmations', [
                'tx_hash' => $txHash,
                'confirmations' => $confirmations,
                'min_confirmations' => $minConfirmations,
            ]);

            // Проверяем существует ли уже эта транзакция
            $existingTransaction = Transaction::where('tx_hash', $txHash)->first();
            $isNewTransaction = !$existingTransaction;

            // Создаём или обновляем pending Transaction для отображения в ЛК/админке
            $transaction = Transaction::updateOrCreate(
                [
                    'tx_hash' => $txHash,
                    'user_id' => $user->id,
                ],
                [
                    'type' => 'deposit',
                    'amount' => $amount,
                    'status' => 'pending',
                    'wallet_address' => $address,
                    'network' => $network,
                    'is_real' => true,
                    'description' => "Deposit of {$amount} {$token} (pending)",
                    'meta' => [
                        'network' => $network,
                        'token' => $token,
                        'address' => $address,
                        'confirmations' => $confirmations,
                        'min_confirmations' => $minConfirmations,
                    ],
                ]
            );

            // Отправляем Telegram уведомление только для новых pending транзакций
            if ($isNewTransaction) {
                $transaction->load('user');
                app(\App\Services\TelegramBotService::class)->sendDepositStatusUpdate($transaction, 'pending');
            }

            return true; // Return true to signal successful webhook receipt
        }

        // Достаточно подтверждений — запускаем Job для окончательного зачисления
        ProcessDepositJob::dispatch($user->id, $amount, $txHash, $network, $token);

        Log::info('Deposit processed successfully', [
            'user_id' => $user->id,
            'amount' => $amount,
            'tx_hash' => $txHash,
        ]);

        return true;
    }

    /**
     * Генерирует адрес для пополнения через API
     */
    public function generateDepositAddress(User $user): ?array
    {
        $apiKey = config('crypto.api_key');
        $network = 'tron';
        $token = 'USDT';

        $response = Http::withHeaders([
            'CCAPI-KEY' => $apiKey,
        ])->get('https://new.cryptocurrencyapi.net/api/tron/.give', [
            'token' => $token,
            'uniqID' => $user->uuid,
            'label' => "Deposit for User #{$user->id}",
        ]);

        if (!$response->successful()) {
            Log::error('Failed to generate deposit address', [
                'user_id' => $user->id,
                'response' => $response->json(),
            ]);
            return null;
        }

        $data = $response->json();
        $address = $data['result']['address'] ?? null;

        if (!$address) {
            return null;
        }

        // Сохраняем адрес в базе
        CryptoAddress::updateOrCreate(
            [
                'user_id' => $user->id,
                'network' => $network,
                'token' => $token,
            ],
            [
                'address' => $address,
                'public_key' => $data['result']['publicKey'] ?? null,
                'address_requested_at' => now(),
            ]
        );

        return [
            'address' => $address,
            'network' => $network,
            'token' => $token,
        ];
    }

    /**
     * Проверяет подпись IPN
     */
    private function verifyIPNSignature(array $data): bool
    {
        $receivedSign = $data['sign'] ?? '';
        unset($data['sign']);

        ksort($data);

        $params = [];
        foreach ($data as $key => $value) {
            if ($value !== '') {
                $params[] = $key . '=' . $value;
            }
        }

        $apiKey = config('crypto.api_key');
        $string = $apiKey . '&' . implode('&', $params);
        $calculatedSign = hash('sha256', $string);

        return $calculatedSign === $receivedSign;
    }

    /**
     * Нормализует название сети из IPN к внутренним ключам конфигурации
     */
    private function normalizeNetwork(string $network): string
    {
        $n = strtolower(trim($network));
        return match ($n) {
            'erc20', 'eth', 'ethereum' => 'ethereum',
            'trc20', 'tron', 'trx' => 'tron',
            'bep20', 'bsc', 'bnb' => 'bsc',
            'polygon', 'matic' => 'polygon',
            'btc', 'bitcoin' => 'bitcoin',
            default => $n,
        };
    }

    /**
     * Находит пользователя по адресу или uniqID
     */
    private function findUserByAddressOrUniqID(string $address, string $uniqID): ?User
    {
        // Сначала пробуем найти по адресу
        $cryptoAddress = CryptoAddress::where('address', $address)->first();
        if ($cryptoAddress) {
            return $cryptoAddress->user;
        }

        // Затем по UUID из uniqID
        if ($uniqID) {
            return User::where('uuid', $uniqID)->first();
        }

        return null;
    }

    /**
     * Проверяет адреса, запрошенные за последние 3 часа
     */
    public function checkRecentAddresses(): int
    {
        $addresses = CryptoAddress::where('address_requested_at', '>=', now()->subHours(3))
            ->get();

        $processedCount = 0;

        foreach ($addresses as $cryptoAddress) {
            $balance = $this->checkAddressBalance(
                $cryptoAddress->network,
                $cryptoAddress->token,
                $cryptoAddress->address
            );

            if ($balance > 0) {
                // Здесь нужно получить транзакции и обработать их
                // Для простоты создаем синтетическую транзакцию
                $this->processDeposit(
                    network: $cryptoAddress->network,
                    token: $cryptoAddress->token,
                    address: $cryptoAddress->address,
                    amount: $balance,
                    txHash: 'manual_check_' . time() . '_' . $cryptoAddress->user_id,
                    confirmations: 100, // Считаем подтвержденным
                    uniqID: $cryptoAddress->user->uuid
                );
                $processedCount++;
            }
        }

        return $processedCount;
    }

    /**
     * Проверяет баланс адреса через API
     */
    private function checkAddressBalance(string $network, string $token, string $address): float
    {
        $apiKey = config('crypto.api_key');

        $response = Http::withHeaders([
            'CCAPI-KEY' => $apiKey,
        ])->get("https://new.cryptocurrencyapi.net/api/{$network}/.balance", [
            'from' => $address,
            'token' => $token,
        ]);

        if (!$response->successful()) {
            return 0;
        }

        $data = $response->json();
        return (float) ($data['result'] ?? 0);
    }
}
