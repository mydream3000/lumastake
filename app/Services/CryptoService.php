<?php

namespace App\Services;

use App\Models\CryptoAddress;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CryptoService
{
    private string $apiKey;
    private string $baseUrl = 'https://new.cryptocurrencyapi.net/api/';

    public function __construct()
    {
        $this->apiKey = config('crypto.api_key');
    }

    /**
     * Генерация или получение существующего адреса для пополнения (USDT/USDC) с защитой от дублей по address
     */
    public function getOrCreateDepositAddress(User $user, string $network = 'tron', string $token = 'USDT'): array
    {
        // Вызываем API для получения адреса (создаст новый или вернет существующий)
        $response = $this->callApi("{$network}/.give", [
            'token' => $token,
            'uniqID' => $user->uuid,
            'label' => "Deposit for User {$user->id}",
            'period' => 180, // 3 часа = 180 минут
            'statusURL' => config('crypto.webhook_url'),
        ]);

        if (!isset($response['result']['address'])) {
            throw new \Exception('Failed to get deposit address: ' . ($response['error'] ?? 'Unknown error'));
        }

        $addressData = $response['result'];
        $address = $addressData['address'];

        // 1) Если такой address уже существует в БД — обновляем его (уникальность по address в схеме)
        $existingByAddress = CryptoAddress::where('address', $address)->first();
        if ($existingByAddress) {
            // Безопасность: адрес не должен принадлежать другому пользователю
            if ($existingByAddress->user_id !== $user->id) {
                Log::error('Deposit address collision: address already assigned to another user', [
                    'address' => $address,
                    'requested_user_id' => $user->id,
                    'existing_user_id' => $existingByAddress->user_id,
                    'network' => $network,
                    'token' => $token,
                ]);
                throw new \Exception('Address is already assigned to another user');
            }

            $existingByAddress->update([
                'network' => $network, // на случай, если провайдер вернет тот же address в другой записи сети
                'token' => $token,     // будет хранить последний запрошенный токен для этого address
                'public_key' => $addressData['publicKey'] ?? null,
                'address_requested_at' => now(),
            ]);

            return [
                'address' => $existingByAddress->address,
                'network' => $network,
                'token' => $token,
                'public_key' => $existingByAddress->public_key,
            ];
        }

        // 2) Иначе пробуем переиспользовать запись по user_id+network (независимо от токена), чтобы держать одну запись на сеть
        $existingByUserNetwork = CryptoAddress::where('user_id', $user->id)
            ->where('network', $network)
            ->first();

        if ($existingByUserNetwork) {
            $existingByUserNetwork->update([
                'token' => $token,
                'address' => $address,
                'public_key' => $addressData['publicKey'] ?? null,
                'address_requested_at' => now(),
            ]);

            return [
                'address' => $existingByUserNetwork->address,
                'network' => $network,
                'token' => $token,
                'public_key' => $existingByUserNetwork->public_key,
            ];
        }

        // 3) Создаем новую запись, если ничего не нашли
        $cryptoAddress = CryptoAddress::create([
            'user_id' => $user->id,
            'network' => $network,
            'token' => $token,
            'address' => $address,
            'public_key' => $addressData['publicKey'] ?? null,
            'address_requested_at' => now(),
        ]);

        return [
            'address' => $cryptoAddress->address,
            'network' => $network,
            'token' => $token,
            'public_key' => $cryptoAddress->public_key,
        ];
    }

    /**
     * Проверка баланса адреса
     */
    public function checkBalance(string $address, string $network = 'tron', string $token = 'USDT'): float
    {
        $response = $this->callApi("{$network}/.balance", [
            'from' => $address,
            'token' => $token,
        ]);

        if (!isset($response['result'])) {
            Log::error('Failed to check balance', [
                'address' => $address,
                'response' => $response,
            ]);
            return 0;
        }

        return (float) $response['result'];
    }

    /**
     * Обработка пополнения для пользователя
     */
    public function processDeposit(User $user, float $amount, string $txid, string $address, string $network, string $token): void
    {
        // Проверяем, не была ли уже обработана эта транзакция
        $existingTransaction = Transaction::where('details->txid', $txid)->first();
        if ($existingTransaction) {
            Log::info('Transaction already processed', ['txid' => $txid]);
            return;
        }

        // Пополняем balance
        $user->balance += $amount;
        $user->save();

        // Проверяем активацию пользователя после увеличения баланса
        if (!$user->active) {
            app(\App\Services\ReferralService::class)->activateUser($user);
        }

        // Создаем запись транзакции
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'deposit',
            'amount' => $amount,
            'status' => 'confirmed',
            'details' => [
                'txid' => $txid,
                'address' => $address,
                'network' => $network,
                'token' => $token,
                'method' => 'crypto',
            ],
        ]);

        // Создаем Toast сообщение для пользователя
        \App\Models\ToastMessage::createForUser(
            $user->id,
            'success',
            "Deposit of {$amount} {$token} has been credited to your account",
            route('cabinet.dashboard')
        );

        Log::info('Crypto deposit processed', [
            'user_id' => $user->id,
            'amount' => $amount,
            'txid' => $txid,
        ]);
    }

    /**
     * Проверка пополнений для пользователей, запросивших адрес в последние 3 часа
     */
    public function checkRecentDeposits(): array
    {
        $threeHoursAgo = Carbon::now()->subHours(3);
        $processed = [];

        // Получаем все адреса, запрошенные в последние 3 часа
        $recentAddresses = CryptoAddress::where('address_requested_at', '>=', $threeHoursAgo)
            ->with('user')
            ->get();

        foreach ($recentAddresses as $cryptoAddress) {
            try {
                // Проверяем баланс адреса
                $currentBalance = $this->checkBalance(
                    $cryptoAddress->address,
                    $cryptoAddress->network,
                    $cryptoAddress->token
                );

                if ($currentBalance > 0) {
                    // Проверяем, не зачислили ли мы уже эти средства
                    // Ищем последнюю транзакцию депозита для этого адреса
                    $lastDeposit = Transaction::where('user_id', $cryptoAddress->user_id)
                        ->where('type', 'deposit')
                        ->where('details->address', $cryptoAddress->address)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    // Если нет предыдущих депозитов или баланс изменился, обрабатываем
                    if (!$lastDeposit || $currentBalance > 0) {
                        // В реальном сценарии нужно отслеживать историю транзакций через API
                        // Пока просто логируем, что есть баланс
                        Log::info('Balance detected on address', [
                            'address' => $cryptoAddress->address,
                            'balance' => $currentBalance,
                            'user_id' => $cryptoAddress->user_id,
                        ]);

                        $processed[] = [
                            'user_id' => $cryptoAddress->user_id,
                            'address' => $cryptoAddress->address,
                            'balance' => $currentBalance,
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error checking deposit', [
                    'address' => $cryptoAddress->address,
                    'error' => $e->getMessage(),
                ]);
            }

            // Соблюдаем rate limit (10 запросов за 2 секунды)
            usleep(250000); // 0.25 секунды между запросами
        }

        return $processed;
    }

    /**
     * Проверка подлинности IPN запроса
     */
    public function verifyIPNSignature(array $data): bool
    {
        $receivedSign = $data['sign'] ?? '';
        unset($data['sign']);

        // Сортируем параметры по ключу
        ksort($data);

        // Формируем строку для хеширования
        $params = [];
        foreach ($data as $key => $value) {
            if ($value !== '') {
                $params[] = $key . '=' . $value;
            }
        }

        $string = $this->apiKey . '&' . implode('&', $params);

        // Вычисляем хеш
        $calculatedSign = hash('sha256', $string);

        return $calculatedSign === $receivedSign;
    }

    /**
     * Вызов API CryptocurrencyAPI.net
     */
    private function callApi(string $endpoint, array $params = []): array
    {
        $url = $this->baseUrl . $endpoint;

        $response = Http::timeout(30)
            ->withHeaders([
                'CCAPI-KEY' => $this->apiKey,
            ])
            ->get($url, $params);

        if (!$response->successful()) {
            Log::error('Crypto API call failed', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Crypto API call failed: ' . $response->body());
        }

        return $response->json();
    }
}
