<?php

namespace App\Console\Commands;

use App\Models\CryptoAddress;
use App\Services\CryptoDepositService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckPendingDeposits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crypto:check-deposits {--address= : Check specific address}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually check for pending deposits on crypto addresses via CryptocurrencyAPI.net';

    /**
     * Execute the console command.
     */
    public function handle(CryptoDepositService $service)
    {
        $this->info('🔍 Checking for pending deposits...');

        $specificAddress = $this->option('address');

        if ($specificAddress) {
            $addresses = CryptoAddress::where('address', $specificAddress)->get();
            if ($addresses->isEmpty()) {
                $this->error("Address {$specificAddress} not found in database");
                return 1;
            }
        } else {
            // Проверяем все адреса, запрошенные за последние 24 часа
            $addresses = CryptoAddress::where('address_requested_at', '>=', now()->subHours(24))->get();
        }

        if ($addresses->isEmpty()) {
            $this->warn('No addresses to check');
            return 0;
        }

        $this->info("Found {$addresses->count()} address(es) to check");

        $apiKey = config('crypto.api_key');
        $processedCount = 0;

        foreach ($addresses as $cryptoAddress) {
            $this->line("Checking address: {$cryptoAddress->address} (User #{$cryptoAddress->user_id})");

            // Получаем транзакции для адреса
            // Попробуем разные endpoints
            $endpoints = [
                "https://new.cryptocurrencyapi.net/api/{$cryptoAddress->network}/.get",
                "https://new.cryptocurrencyapi.net/api/{$cryptoAddress->network}/.history",
            ];

            $response = null;
            $transactions = [];

            foreach ($endpoints as $endpoint) {
                $this->line("  Trying endpoint: {$endpoint}");

                $response = Http::withHeaders([
                    'CCAPI-KEY' => $apiKey,
                ])->get($endpoint, [
                    'address' => $cryptoAddress->address,
                    'token' => $cryptoAddress->token,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $this->line("  Response: " . json_encode($data));

                    // Проверяем разные форматы ответа
                    if (isset($data['result']) && is_array($data['result'])) {
                        if (isset($data['result']['transactions'])) {
                            $transactions = $data['result']['transactions'];
                        } elseif (isset($data['result'][0])) {
                            $transactions = $data['result'];
                        } elseif (isset($data['result']['value'])) {
                            // Это ответ от .get endpoint с балансом
                            $balance = (float) ($data['result']['value'] ?? 0);
                            if ($balance > 0) {
                                $this->info("  Balance found: {$balance} {$cryptoAddress->token}");
                                // Создаём синтетическую транзакцию
                                $transactions = [[
                                    'value' => $balance,
                                    'txid' => 'balance_check_' . time(),
                                    'confirmations' => 100,
                                ]];
                            }
                        }
                    }

                    if (!empty($transactions)) {
                        break;
                    }
                }
            }

            if (!$response || !$response->successful()) {
                $this->error("  ❌ Failed to get history for {$cryptoAddress->address}");
                Log::error('Failed to get address history', [
                    'address' => $cryptoAddress->address,
                    'response' => $response ? $response->json() : 'No response',
                ]);
                continue;
            }

            if (empty($transactions)) {
                $this->line("  ℹ️  No transactions found");
                continue;
            }

            $this->info("  📋 Found " . count($transactions) . " transaction(s)");

            foreach ($transactions as $tx) {
                $amount = (float) ($tx['value'] ?? 0);
                $txHash = $tx['txid'] ?? '';
                $confirmations = (int) ($tx['confirmations'] ?? 0);

                $this->line("    TX: {$txHash}");
                $this->line("    Amount: {$amount} {$cryptoAddress->token}");
                $this->line("    Confirmations: {$confirmations}");

                if ($amount > 0) {
                    // Обрабатываем депозит
                    $processed = $service->processDeposit(
                        network: $cryptoAddress->network,
                        token: $cryptoAddress->token,
                        address: $cryptoAddress->address,
                        amount: $amount,
                        txHash: $txHash,
                        confirmations: $confirmations,
                        uniqID: $cryptoAddress->user->uuid
                    );

                    if ($processed) {
                        $this->info("    ✅ Deposit processed successfully");
                        $processedCount++;
                    } else {
                        $this->warn("    ⚠️  Deposit not processed (may be already processed or insufficient confirmations)");
                    }
                }
            }
        }

        $this->info("✅ Done! Processed {$processedCount} deposit(s)");
        return 0;
    }
}
