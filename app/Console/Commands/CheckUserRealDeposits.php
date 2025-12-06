<?php

namespace App\Console\Commands;

use App\Models\CryptoTransaction;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Console\Command;

class CheckUserRealDeposits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deposits:check-user {userId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check user real deposits and fix processed flag';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('userId');
        $user = User::find($userId);

        if (!$user) {
            $this->error("User #{$userId} not found");
            return 1;
        }

        $this->info("=== User #{$userId}: {$user->name} ({$user->email}) ===");
        $this->info("Current balance: \${$user->balance}");
        $this->newLine();

        // 1. Все крипто-транзакции пользователя
        $this->info("--- CRYPTO TRANSACTIONS ---");
        $cryptoTxs = CryptoTransaction::where('user_id', $userId)
            ->whereIn('token', ['USDT', 'USDC'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($cryptoTxs->isEmpty()) {
            $this->warn("No crypto transactions found");
        } else {
            $totalAll = 0;
            $totalProcessed = 0;

            foreach ($cryptoTxs as $tx) {
                $status = $tx->processed ? '✓ PROCESSED' : '✗ NOT PROCESSED';
                $this->line(sprintf(
                    "ID:%d | %s | %s | \$%.2f | %d conf | %s | %s",
                    $tx->id,
                    $tx->network,
                    $tx->token,
                    $tx->amount,
                    $tx->confirmations,
                    $status,
                    $tx->created_at->format('Y-m-d H:i')
                ));

                $totalAll += $tx->amount;
                if ($tx->processed) {
                    $totalProcessed += $tx->amount;
                }
            }

            $this->newLine();
            $this->info("Total ALL crypto: \$" . number_format($totalAll, 2));
            $this->info("Total PROCESSED: \$" . number_format($totalProcessed, 2));
            $this->info("Total NOT PROCESSED: \$" . number_format($totalAll - $totalProcessed, 2));
        }

        $this->newLine();

        // 2. Все транзакции типа deposit со статусом confirmed
        $this->info("--- TRANSACTIONS (type=deposit, status=confirmed) ---");
        $transactions = Transaction::where('user_id', $userId)
            ->where('type', 'deposit')
            ->where('status', 'confirmed')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($transactions->isEmpty()) {
            $this->warn("No confirmed deposit transactions found");
        } else {
            $totalConfirmed = 0;

            foreach ($transactions as $tx) {
                $this->line(sprintf(
                    "ID:%d | \$%.2f | %s | %s",
                    $tx->id,
                    $tx->amount,
                    $tx->tx_hash ?? 'no hash',
                    $tx->created_at->format('Y-m-d H:i')
                ));

                $totalConfirmed += $tx->amount;
            }

            $this->newLine();
            $this->info("Total CONFIRMED deposits: \$" . number_format($totalConfirmed, 2));
        }

        $this->newLine();

        // 3. Проверка: есть ли confirmed транзакции без processed crypto_transactions
        $this->info("--- CHECKING FOR MISMATCHES ---");

        $confirmedTxHashes = Transaction::where('user_id', $userId)
            ->where('type', 'deposit')
            ->where('status', 'confirmed')
            ->whereNotNull('tx_hash')
            ->pluck('tx_hash');

        $fixed = 0;

        foreach ($confirmedTxHashes as $txHash) {
            $cryptoTx = CryptoTransaction::where('tx_hash', $txHash)->first();

            if ($cryptoTx && !$cryptoTx->processed) {
                $this->warn("Found confirmed transaction with unprocessed crypto_tx: {$txHash}");

                if ($this->confirm("Mark this crypto_tx as processed?", true)) {
                    $cryptoTx->update(['processed' => true]);
                    $this->info("✓ Fixed: {$txHash}");
                    $fixed++;
                }
            }
        }

        if ($fixed > 0) {
            $this->newLine();
            $this->info("Fixed {$fixed} crypto transactions");
        } else {
            $this->info("No mismatches found - all looks good!");
        }

        return 0;
    }
}
