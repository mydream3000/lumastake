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
    public function checkRecentAddresses(?string $specificAddress = null): int
    {
        $query = CryptoAddress::query();

        if ($specificAddress) {
            $query->where('address', $specificAddress);
        } else {
            // Если адрес не указан, проверяем только недавно запрошенные (за последние 24 часа)
            $query->where('address_requested_at', '>=', now()->subHours(24));
        }

        $addresses = $query->get();
        $processedCount = 0;

        foreach ($addresses as $cryptoAddress) {
            $transactions = $this->getBlockchainTransactions($cryptoAddress->address, $cryptoAddress->network, $cryptoAddress->token);

            foreach ($transactions as $tx) {
                if ($tx['amount'] > 0) {
                    $processed = $this->processDeposit(
                        network: $cryptoAddress->network,
                        token: $cryptoAddress->token,
                        address: $cryptoAddress->address,
                        amount: $tx['amount'],
                        txHash: $tx['hash'],
                        confirmations: $tx['confirmations'],
                        uniqID: $cryptoAddress->user->uuid
                    );

                    if ($processed) {
                        $processedCount++;
                    }
                }
            }
        }

        return $processedCount;
    }

    /**
     * Получает транзакции из блокчейна (TronScan/Etherscan/BscScan)
     */
    public function getBlockchainTransactions(string $address, string $network, string $token): array
    {
        return match ($network) {
            'tron' => $this->getTronTransactions($address, $token),
            'ethereum' => $this->getEthereumTransactions($address, $token),
            'bsc' => $this->getBscTransactions($address, $token),
            default => [],
        };
    }

    private function getTronTransactions(string $address, string $token): array
    {
        try {
            // TRC20 USDT contract: TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t
            $contractAddress = 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t';

            // Используем TronGrid API вместо TronScan (который часто требует API-ключ или выдает 401)
            $response = Http::timeout(10)->get("https://api.trongrid.io/v1/accounts/{$address}/transactions/trc20", [
                'limit' => 20,
                'only_to' => 'true',
                'contract_address' => $contractAddress,
            ]);

            if (!$response->successful()) {
                Log::error('TronGrid API check failed', [
                    'status' => $response->status(),
                    'address' => $address,
                    'response' => $response->body()
                ]);
                return [];
            }

            $data = $response->json();
            $transfers = $data['data'] ?? [];
            $transactions = [];

            foreach ($transfers as $transfer) {
                if (strtolower($transfer['to']) === strtolower($address)) {
                    $txHash = $transfer['transaction_id'];
                    $decimals = (int) ($transfer['token_info']['decimals'] ?? 6);
                    $rawValue = $transfer['value'];

                    // TronGrid возвращает уже подтвержденные транзакции в этом эндпоинте.
                    // Устанавливаем 20 подтверждений для прохождения внутренних проверок.
                    $confirmations = 20;

                    $transactions[] = [
                        'hash' => $txHash,
                        'amount' => (float) $rawValue / pow(10, $decimals),
                        'confirmations' => $confirmations,
                    ];
                }
            }
            return $transactions;
        } catch (\Exception $e) {
            Log::error('Tron check failed (TronGrid)', ['error' => $e->getMessage()]);
            return [];
        }
    }

    private function getEthereumTransactions(string $address, string $token): array
    {
        try {
            $contractAddress = '0xdAC17F958D2ee523a2206206994597C13D831ec7';
            $response = Http::get("https://api.etherscan.io/api", [
                'module' => 'account',
                'action' => 'tokentx',
                'contractaddress' => $contractAddress,
                'address' => $address,
                'page' => 1,
                'offset' => 20,
                'sort' => 'desc',
            ]);

            if (!$response->successful()) return [];

            $data = $response->json();
            $result = $data['result'] ?? [];
            if (!is_array($result)) return [];

            $transactions = [];
            foreach ($result as $tx) {
                if (strtolower($tx['to']) === strtolower($address)) {
                    $transactions[] = [
                        'hash' => $tx['hash'],
                        'amount' => $tx['value'] / 1000000,
                        'confirmations' => (int) ($tx['confirmations'] ?? 0),
                    ];
                }
            }
            return $transactions;
        } catch (\Exception $e) {
            Log::error('Etherscan check failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    private function getBscTransactions(string $address, string $token): array
    {
        try {
            $contractAddress = '0x55d398326f99059fF775485246999027B3197955';
            $response = Http::get("https://api.bscscan.com/api", [
                'module' => 'account',
                'action' => 'tokentx',
                'contractaddress' => $contractAddress,
                'address' => $address,
                'page' => 1,
                'offset' => 20,
                'sort' => 'desc',
            ]);

            if (!$response->successful()) return [];

            $data = $response->json();
            $result = $data['result'] ?? [];
            if (!is_array($result)) return [];

            $transactions = [];
            foreach ($result as $tx) {
                if (strtolower($tx['to']) === strtolower($address)) {
                    $transactions[] = [
                        'hash' => $tx['hash'],
                        'amount' => $tx['value'] / 1e18, // BSC USDT usually has 18 decimals
                        'confirmations' => (int) ($tx['confirmations'] ?? 0),
                    ];
                }
            }
            return $transactions;
        } catch (\Exception $e) {
            Log::error('BscScan check failed', ['error' => $e->getMessage()]);
            return [];
        }
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
