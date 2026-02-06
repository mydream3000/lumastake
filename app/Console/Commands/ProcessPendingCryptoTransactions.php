<?php

namespace App\Console\Commands;

use App\Jobs\ProcessDepositJob;
use App\Models\CryptoTransaction;
use App\Models\Transaction;
use Illuminate\Console\Command;

class ProcessPendingCryptoTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crypto:process-pending {--id= : Process specific transaction by ID} {--all : Process all unprocessed transactions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending crypto transactions that were previously skipped (e.g., due to minimum amount check)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $transactionId = $this->option('id');
        $processAll = $this->option('all');

        if ($transactionId) {
            // Обработка конкретной транзакции по ID
            return $this->processSingleTransaction((int) $transactionId);
        }

        if ($processAll) {
            // Обработка всех непроцессированных транзакций
            return $this->processAllPendingTransactions();
        }

        $this->error('Укажите --id=<ID> для обработки конкретной транзакции или --all для обработки всех');
        return 1;
    }

    private function processSingleTransaction(int $id): int
    {
        $transaction = CryptoTransaction::find($id);

        if (!$transaction) {
            $this->error("Транзакция с ID {$id} не найдена");
            return 1;
        }

        if ($transaction->processed) {
            // Проверяем, действительно ли баланс зачислен
            $hasConfirmed = Transaction::where('tx_hash', $transaction->tx_hash)
                ->where('status', 'confirmed')
                ->exists();

            if ($hasConfirmed) {
                $this->warn("Транзакция #{$id} уже обработана и баланс зачислен");
                return 0;
            }

            $this->warn("Транзакция #{$id} помечена как processed, но баланс НЕ зачислен! Повторная обработка...");
        }

        $this->info("Обработка транзакции #{$id}...");
        $this->info("User ID: {$transaction->user_id}");
        $this->info("Amount: {$transaction->amount} {$transaction->token}");
        $this->info("Network: {$transaction->network}");
        $this->info("TX Hash: {$transaction->tx_hash}");

        // Запускаем Job для зачисления (Job сам обновит processed = true после успешного зачисления)
        ProcessDepositJob::dispatch(
            $transaction->user_id,
            (float) $transaction->amount,
            $transaction->tx_hash,
            $transaction->network,
            $transaction->token
        );

        $this->info("✓ Транзакция #{$id} успешно отправлена на обработку");

        return 0;
    }

    private function processAllPendingTransactions(): int
    {
        // 1. Стандартные необработанные транзакции (processed = false)
        $unprocessed = CryptoTransaction::where('processed', false)
            ->where('confirmations', '>=', 9)
            ->get();

        // 2. "Застрявшие" транзакции: processed = true, но баланс не зачислен
        //    (CryptoTransaction помечена processed, но Transaction не confirmed)
        $stuck = CryptoTransaction::where('processed', true)
            ->whereDoesntHave('confirmedTransaction')
            ->get();

        $transactions = $unprocessed->merge($stuck);

        if ($transactions->isEmpty()) {
            $this->info('Нет необработанных транзакций');
            return 0;
        }

        if ($unprocessed->isNotEmpty()) {
            $this->info("Найдено {$unprocessed->count()} необработанных транзакций");
        }
        if ($stuck->isNotEmpty()) {
            $this->warn("Найдено {$stuck->count()} застрявших транзакций (processed=true, но баланс не зачислен)");
        }

        $progressBar = $this->output->createProgressBar($transactions->count());
        $processed = 0;

        foreach ($transactions as $transaction) {
            ProcessDepositJob::dispatch(
                $transaction->user_id,
                (float) $transaction->amount,
                $transaction->tx_hash,
                $transaction->network,
                $transaction->token
            );

            $processed++;
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        $this->info("✓ Отправлено на обработку транзакций: {$processed}");

        return 0;
    }
}
