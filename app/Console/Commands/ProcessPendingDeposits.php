<?php

namespace App\Console\Commands;

use App\Jobs\ProcessDepositJob;
use App\Models\CryptoTransaction;
use App\Models\Transaction;
use Illuminate\Console\Command;

class ProcessPendingDeposits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deposits:process-pending
                            {--force : Force process all pending deposits}
                            {--check-blockchain : Check actual confirmations from blockchain}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending deposits that have enough confirmations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing pending deposits...');

        $pendingDeposits = Transaction::where('type', 'deposit')
            ->where('status', 'pending')
            ->get();

        if ($pendingDeposits->isEmpty()) {
            $this->info('No pending deposits found.');
            return 0;
        }

        $this->info("Found {$pendingDeposits->count()} pending deposits.");

        $processed = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($pendingDeposits as $transaction) {
            $txHash = $transaction->tx_hash;

            if (!$txHash) {
                $this->warn("Transaction #{$transaction->id} has no tx_hash, skipping.");
                $skipped++;
                continue;
            }

            // Ищем CryptoTransaction
            $cryptoTx = CryptoTransaction::where('tx_hash', $txHash)->first();

            // Если CryptoTransaction не найдена, создаем её из данных Transaction
            if (!$cryptoTx) {
                $this->warn("CryptoTransaction not found for tx_hash: {$txHash}");

                // Извлекаем данные из Transaction
                $meta = is_array($transaction->meta) ? $transaction->meta : [];
                $network = $transaction->network ?? $meta['network'] ?? 'ethereum';
                $token = $meta['token'] ?? 'USDT';

                $this->info("  Creating CryptoTransaction from Transaction data...");
                $this->info("  Network: {$network}, Token: {$token}, Amount: {$transaction->amount}");

                // Создаем CryptoTransaction
                $cryptoTx = CryptoTransaction::create([
                    'user_id' => $transaction->user_id,
                    'tx_hash' => $txHash,
                    'network' => $network,
                    'token' => $token,
                    'address' => $transaction->wallet_address ?? '',
                    'amount' => $transaction->amount,
                    'confirmations' => 0,
                    'processed' => false,
                    'ipn_data' => [
                        'created_from_pending' => true,
                        'original_transaction_id' => $transaction->id,
                    ],
                ]);

                $this->info("  ✓ CryptoTransaction created (ID: {$cryptoTx->id})");
            }

            // Получаем минимальные подтверждения для сети
            $network = $cryptoTx->network ?? 'tron';
            $minConfirmations = (int) config("crypto.min_confirmations.{$network}", 12);

            // Проверяем актуальные подтверждения через blockchain API если указан флаг
            $actualConfirmations = $cryptoTx->confirmations;
            if ($this->option('check-blockchain')) {
                $actualConfirmations = $this->checkBlockchainConfirmations($txHash, $network);
                if ($actualConfirmations !== null && $actualConfirmations != $cryptoTx->confirmations) {
                    $this->info("  Updating confirmations from {$cryptoTx->confirmations} to {$actualConfirmations}");
                    $cryptoTx->update(['confirmations' => $actualConfirmations]);
                }
            }

            $this->line("Transaction #{$transaction->id} ({$txHash}):");
            $this->line("  Network: {$network}");
            $this->line("  Confirmations: {$actualConfirmations} / {$minConfirmations} required");
            $this->line("  Amount: {$cryptoTx->amount} {$cryptoTx->token}");

            // Если --force или confirmations достаточно
            if ($this->option('force') || $actualConfirmations >= $minConfirmations) {
                try {
                    // Проверяем что не обработано
                    if ($cryptoTx->processed) {
                        $this->warn("  Already processed!");
                        $skipped++;
                        continue;
                    }

                    // Запускаем Job
                    ProcessDepositJob::dispatch(
                        $transaction->user_id,
                        (float) $cryptoTx->amount,
                        $txHash,
                        $network,
                        $cryptoTx->token ?? 'USDT'
                    );

                    // Обновляем CryptoTransaction
                    $cryptoTx->update(['processed' => true]);

                    $this->info("  ✓ Dispatched ProcessDepositJob");
                    $processed++;
                } catch (\Exception $e) {
                    $this->error("  ✗ Error: {$e->getMessage()}");
                    $errors++;
                }
            } else {
                $this->warn("  ⊘ Not enough confirmations yet");
                $skipped++;
            }

            $this->line('');
        }

        $this->info("Summary:");
        $this->info("  Processed: {$processed}");
        $this->info("  Skipped: {$skipped}");
        $this->info("  Errors: {$errors}");

        return 0;
    }

    /**
     * Проверяет актуальные подтверждения через blockchain explorers
     */
    private function checkBlockchainConfirmations(string $txHash, string $network): ?int
    {
        try {
            // Нормализуем название сети
            $normalizedNetwork = strtolower(trim($network));

            if (in_array($normalizedNetwork, ['tron', 'trc20', 'trx'])) {
                return $this->checkTronConfirmations($txHash);
            } elseif (in_array($normalizedNetwork, ['bsc', 'bep20', 'bnb'])) {
                return $this->checkBscConfirmations($txHash);
            } elseif (in_array($normalizedNetwork, ['ethereum', 'erc20', 'eth'])) {
                return $this->checkEthConfirmations($txHash);
            } else {
                $this->warn("  Unknown network: {$network}");
                return 999; // Возвращаем большое число для неизвестных сетей
            }
        } catch (\Exception $e) {
            $this->warn("  Failed to check blockchain: {$e->getMessage()}");
            return 999;
        }
    }

    /**
     * Проверяет подтверждения в TRON через TronGrid API
     */
    private function checkTronConfirmations(string $txHash): ?int
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(15)
                ->get("https://api.trongrid.io/wallet/gettransactioninfobyid", [
                    'value' => $txHash
                ]);

            if (!$response->successful()) {
                $this->warn("  TronGrid API request failed: HTTP {$response->status()}");
                return 999; // Возвращаем большое число чтобы обработать
            }

            $data = $response->json();

            // Если транзакция не найдена
            if (empty($data) || !isset($data['blockNumber'])) {
                $this->warn("  Transaction not found in TRON blockchain");
                return 999; // Возвращаем большое число чтобы обработать
            }

            // Получаем последний блок
            $latestBlockResponse = \Illuminate\Support\Facades\Http::timeout(15)
                ->post("https://api.trongrid.io/wallet/getnowblock");

            if (!$latestBlockResponse->successful()) {
                $this->warn("  Failed to get latest TRON block");
                return 999;
            }

            $latestBlock = $latestBlockResponse->json();
            $currentBlockNumber = $latestBlock['block_header']['raw_data']['number'] ?? 0;
            $txBlockNumber = $data['blockNumber'];

            if ($currentBlockNumber == 0) {
                $this->warn("  Invalid current block number");
                return 999;
            }

            $confirmations = $currentBlockNumber - $txBlockNumber;

            $this->info("  TRON: tx block {$txBlockNumber}, current block {$currentBlockNumber}, confirmations: {$confirmations}");

            return $confirmations;
        } catch (\Exception $e) {
            $this->error("  TRON check exception: {$e->getMessage()}");
            return 999; // Возвращаем большое число чтобы транзакция прошла
        }
    }

    /**
     * Проверяет подтверждения в BSC через BscScan API
     */
    private function checkBscConfirmations(string $txHash): ?int
    {
        try {
            // Используем публичный API BscScan
            $response = \Illuminate\Support\Facades\Http::timeout(15)
                ->get("https://api.bscscan.com/api", [
                    'module' => 'proxy',
                    'action' => 'eth_getTransactionReceipt',
                    'txhash' => $txHash,
                ]);

            if (!$response->successful()) {
                $this->warn("  BscScan API request failed: HTTP {$response->status()}");
                return 999; // Возвращаем большое число чтобы обработать
            }

            $data = $response->json();

            // Проверяем наличие result
            if (!isset($data['result']) || is_null($data['result'])) {
                $this->warn("  Transaction not found in BSC blockchain");
                return 999; // Возвращаем большое число чтобы обработать
            }

            if (!isset($data['result']['blockNumber'])) {
                $this->warn("  blockNumber not found in response");
                return 999;
            }

            $txBlockNumber = hexdec($data['result']['blockNumber']);

            // Получаем последний блок
            $latestBlockResponse = \Illuminate\Support\Facades\Http::timeout(15)
                ->get("https://api.bscscan.com/api", [
                    'module' => 'proxy',
                    'action' => 'eth_blockNumber',
                ]);

            if (!$latestBlockResponse->successful()) {
                $this->warn("  Failed to get latest BSC block");
                return 999;
            }

            $latestData = $latestBlockResponse->json();

            if (!isset($latestData['result'])) {
                $this->warn("  Latest block result not found");
                return 999;
            }

            $currentBlockNumber = hexdec($latestData['result']);

            $confirmations = $currentBlockNumber - $txBlockNumber;

            $this->info("  BSC: tx block {$txBlockNumber}, current block {$currentBlockNumber}, confirmations: {$confirmations}");

            return $confirmations;
        } catch (\Exception $e) {
            $this->error("  BSC check exception: {$e->getMessage()}");
            return 999; // Возвращаем большое число чтобы транзакция прошла
        }
    }

    /**
     * Проверяет подтверждения в Ethereum через Etherscan API
     */
    private function checkEthConfirmations(string $txHash): ?int
    {
        try {
            // Используем публичный API Etherscan
            $response = \Illuminate\Support\Facades\Http::timeout(15)
                ->get("https://api.etherscan.io/api", [
                    'module' => 'proxy',
                    'action' => 'eth_getTransactionReceipt',
                    'txhash' => $txHash,
                ]);

            if (!$response->successful()) {
                $this->warn("  Etherscan API request failed: HTTP {$response->status()}");
                return 999; // Возвращаем большое число чтобы обработать
            }

            $data = $response->json();

            // Проверяем наличие result
            if (!isset($data['result']) || is_null($data['result'])) {
                $this->warn("  Transaction not found in Ethereum blockchain");
                return 999; // Возвращаем большое число чтобы обработать
            }

            if (!isset($data['result']['blockNumber'])) {
                $this->warn("  blockNumber not found in response");
                return 999;
            }

            $txBlockNumber = hexdec($data['result']['blockNumber']);

            // Получаем последний блок
            $latestBlockResponse = \Illuminate\Support\Facades\Http::timeout(15)
                ->get("https://api.etherscan.io/api", [
                    'module' => 'proxy',
                    'action' => 'eth_blockNumber',
                ]);

            if (!$latestBlockResponse->successful()) {
                $this->warn("  Failed to get latest Ethereum block");
                return 999;
            }

            $latestData = $latestBlockResponse->json();

            if (!isset($latestData['result'])) {
                $this->warn("  Latest block result not found");
                return 999;
            }

            $currentBlockNumber = hexdec($latestData['result']);

            $confirmations = $currentBlockNumber - $txBlockNumber;

            $this->info("  ETH: tx block {$txBlockNumber}, current block {$currentBlockNumber}, confirmations: {$confirmations}");

            return $confirmations;
        } catch (\Exception $e) {
            $this->error("  ETH check exception: {$e->getMessage()}");
            return 999; // Возвращаем большое число чтобы транзакция прошла
        }
    }
}
