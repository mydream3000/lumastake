<?php

namespace App\Console\Commands;

use App\Models\CryptoAddress;
use App\Services\CryptoDepositService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckDepositsViaBlockchain extends Command
{
    protected $signature = 'crypto:check-blockchain {--address= : Check specific address}';
    protected $description = 'Check deposits directly from blockchain explorers (TronScan/Etherscan) - WORKS WITHOUT CryptocurrencyAPI!';

    public function handle(CryptoDepositService $service)
    {
        $this->info('🔗 Checking deposits via blockchain explorers...');

        // 1. Проверяем транзакции со статусом "processing" (пользователь нажал "Я оплатил")
        $this->checkProcessingTransactions();

        // 2. Проверяем все адреса, запрошенные за последние 24 часа
        $this->checkCryptoAddresses($service);

        return 0;
    }

    /**
     * Проверка транзакций со статусом "processing"
     */
    private function checkProcessingTransactions()
    {
        $this->info('🔍 Checking processing transactions...');

        $processingTransactions = \App\Models\Transaction::where('status', 'processing')
            ->where('type', 'deposit')
            ->where('created_at', '>=', now()->subHours(24))
            ->get();

        if ($processingTransactions->isEmpty()) {
            $this->line('  ℹ️  No processing transactions');
            return;
        }

        $this->info("  📋 Found {$processingTransactions->count()} processing transaction(s)");

        foreach ($processingTransactions as $transaction) {
            $this->line("  Checking TX #{$transaction->id} for {$transaction->wallet_address}");

            $blockchainTxs = $this->getBlockchainTransactionsByAddress(
                $transaction->wallet_address,
                $transaction->network,
                'USDT'
            );

            if (empty($blockchainTxs)) {
                $this->line("    ℹ️  No blockchain transactions found yet");
                continue;
            }

            foreach ($blockchainTxs as $btx) {
                // Проверяем совпадает ли сумма
                if (abs($btx['amount'] - $transaction->amount) < 0.01) {
                    $this->info("    ✅ Found matching transaction: {$btx['hash']}");
                    $this->info("       Confirmations: {$btx['confirmations']}");

                    // Обновляем tx_hash если его нет
                    if (!$transaction->tx_hash) {
                        $transaction->tx_hash = $btx['hash'];
                    }

                    // Обновляем статус в зависимости от confirmations
                    if ($btx['confirmations'] >= 1 && $transaction->status === 'processing') {
                        $transaction->status = 'pending';
                        $transaction->notes = "Transaction found in blockchain. {$btx['confirmations']} confirmation(s).";
                        $transaction->save();

                        $this->info("       📢 Status updated: processing → pending");

                        // Telegram уведомление
                        app(\App\Services\TelegramBotService::class)->sendDepositStatusUpdate($transaction, 'pending');
                    }

                    // Если достаточно confirmations - помечаем как confirmed и начисляем баланс
                    $requiredConfirmations = $transaction->network === 'tron' ? 20 : 12;
                    if ($btx['confirmations'] >= $requiredConfirmations && $transaction->status === 'pending') {
                        $transaction->status = 'confirmed';
                        $transaction->notes = "Confirmed with {$btx['confirmations']} confirmations.";
                        $transaction->save();

                        // Начисляем баланс
                        $user = $transaction->user;
                        $user->balance += $transaction->amount;
                        $user->deposited += $transaction->amount;
                        $user->save();

                        // Проверяем активацию пользователя после увеличения баланса
                        if (!$user->active) {
                            app(\App\Services\ReferralService::class)->activateUser($user);
                        }

                        $this->info("       📢 Status updated: pending → confirmed");
                        $this->info("       💰 Balance credited: +{$transaction->amount} USDT");

                        // Telegram уведомление
                        app(\App\Services\TelegramBotService::class)->sendDepositStatusUpdate($transaction, 'confirmed');

                        // Toast сообщение
                        \App\Models\ToastMessage::create([
                            'user_id' => $user->id,
                            'message' => "Deposit confirmed! +{$transaction->amount} USDT credited to your balance.",
                            'type' => 'success',
                        ]);
                    }

                    break; // Нашли нужную транзакцию, прерываем цикл
                }
            }
        }
    }

    /**
     * Проверка адресов из crypto_addresses (старая логика)
     */
    private function checkCryptoAddresses(CryptoDepositService $service)
    {
        $this->info('🔍 Checking crypto addresses...');

        $specificAddress = $this->option('address');

        if ($specificAddress) {
            $addresses = \App\Models\CryptoAddress::where('address', $specificAddress)->get();
        } else {
            $addresses = \App\Models\CryptoAddress::where('address_requested_at', '>=', now()->subHours(24))->get();
        }

        if ($addresses->isEmpty()) {
            $this->line('  ℹ️  No addresses to check');
            return;
        }

        $this->info("  📋 Found {$addresses->count()} address(es) to check");
        $processedCount = 0;

        foreach ($addresses as $cryptoAddress) {
            $this->line("  Checking: {$cryptoAddress->address} (User #{$cryptoAddress->user_id}, {$cryptoAddress->network})");

            $transactions = $this->getBlockchainTransactions($cryptoAddress);

            if (empty($transactions)) {
                $this->line("    ℹ️  No incoming transactions found");
                continue;
            }

            $this->info("    📋 Found " . count($transactions) . " transaction(s)");

            foreach ($transactions as $tx) {
                $this->line("      TX: {$tx['hash']}");
                $this->line("      Amount: {$tx['amount']} {$cryptoAddress->token}");
                $this->line("      Confirmations: {$tx['confirmations']}");

                if ($tx['amount'] > 0) {
                    $processed = $service->processDeposit(
                        network: $cryptoAddress->network,
                        token: $cryptoAddress->token,
                        address: $cryptoAddress->address,
                        amount: $tx['amount'],
                        txHash: $tx['hash'],
                        confirmations: $tx['confirmations'],
                        uniqID: $cryptoAddress->user->uuid
                    );

                    if ($processed) {
                        $this->info("      ✅ Deposit processed");
                        $processedCount++;
                    } else {
                        $this->warn("      ⚠️  Not processed (may be already credited or insufficient confirmations)");
                    }
                }
            }
        }

        $this->info("✅ Done! Processed {$processedCount} deposit(s) from crypto addresses");
    }

    /**
     * Получение транзакций из blockchain для конкретного адреса
     */
    private function getBlockchainTransactionsByAddress(string $address, string $network, string $token): array
    {
        return app(CryptoDepositService::class)->getBlockchainTransactions($address, $network, $token);
    }

    private function getBlockchainTransactions(CryptoAddress $address): array
    {
        return $this->getBlockchainTransactionsByAddress($address->address, $address->network, $address->token);
    }

    private function getTronTransactions(string $address, string $token): array
    {
        // Method moved to CryptoDepositService
        return $this->getBlockchainTransactionsByAddress($address, 'tron', $token);
    }

    private function getEthereumTransactions(string $address, string $token): array
    {
        // Method moved to CryptoDepositService
        return $this->getBlockchainTransactionsByAddress($address, 'ethereum', $token);
    }

    private function getBscTransactions(string $address, string $token): array
    {
        // Method moved to CryptoDepositService
        return $this->getBlockchainTransactionsByAddress($address, 'bsc', $token);
    }
}
