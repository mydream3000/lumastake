<?php

namespace App\Console\Commands;

use App\Jobs\ProcessDepositJob;
use App\Models\CryptoTransaction;
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
            $this->warn("Транзакция #{$id} уже обработана");
            return 0;
        }

        $this->info("Обработка транзакции #{$id}...");
        $this->info("User ID: {$transaction->user_id}");
        $this->info("Amount: {$transaction->amount} {$transaction->token}");
        $this->info("Network: {$transaction->network}");
        $this->info("TX Hash: {$transaction->tx_hash}");

        // Запускаем Job для зачисления
        ProcessDepositJob::dispatch(
            $transaction->user_id,
            $transaction->amount,
            $transaction->tx_hash,
            $transaction->network,
            $transaction->token
        );

        // Обновляем флаг processed
        $transaction->update(['processed' => true]);

        $this->info("✓ Транзакция #{$id} успешно отправлена на обработку");

        return 0;
    }

    private function processAllPendingTransactions(): int
    {
        $transactions = CryptoTransaction::where('processed', false)
            ->where('confirmations', '>=', 9) // Минимум для Tron
            ->get();

        if ($transactions->isEmpty()) {
            $this->info('Нет необработанных транзакций');
            return 0;
        }

        $this->info("Найдено {$transactions->count()} необработанных транзакций");

        $progressBar = $this->output->createProgressBar($transactions->count());
        $processed = 0;

        foreach ($transactions as $transaction) {
            ProcessDepositJob::dispatch(
                $transaction->user_id,
                $transaction->amount,
                $transaction->tx_hash,
                $transaction->network,
                $transaction->token
            );

            $transaction->update(['processed' => true]);
            $processed++;
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        $this->info("✓ Обработано транзакций: {$processed}");

        return 0;
    }
}
