<?php

namespace App\Console\Commands;

use App\Models\CryptoTransaction;
use App\Models\Transaction;
use Illuminate\Console\Command;

class FixAllProcessedFlags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deposits:fix-processed-flags {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix processed flags for crypto_transactions based on confirmed deposits';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info("Running in DRY RUN mode - no changes will be made");
        }

        $this->info("Finding confirmed deposits with unprocessed crypto transactions...");
        $this->newLine();

        // Находим все confirmed транзакции с tx_hash
        $confirmedTxHashes = Transaction::where('type', 'deposit')
            ->where('status', 'confirmed')
            ->whereNotNull('tx_hash')
            ->pluck('tx_hash', 'id')
            ->toArray();

        $this->info("Found " . count($confirmedTxHashes) . " confirmed deposit transactions");

        $fixed = 0;
        $alreadyOk = 0;
        $missing = 0;

        foreach ($confirmedTxHashes as $transactionId => $txHash) {
            $cryptoTx = CryptoTransaction::where('tx_hash', $txHash)->first();

            if (!$cryptoTx) {
                $missing++;
                $this->warn("Transaction #{$transactionId}: No crypto_transaction found for tx_hash: {$txHash}");
                continue;
            }

            if (!$cryptoTx->processed) {
                $this->line("Transaction #{$transactionId}: Crypto TX #{$cryptoTx->id} needs fixing (user_id: {$cryptoTx->user_id}, amount: \${$cryptoTx->amount})");

                if (!$isDryRun) {
                    $cryptoTx->update(['processed' => true]);
                    $this->info("  ✓ Fixed");
                }

                $fixed++;
            } else {
                $alreadyOk++;
            }
        }

        $this->newLine();
        $this->info("=== SUMMARY ===");
        $this->info("Total confirmed deposits: " . count($confirmedTxHashes));
        $this->info("Already OK: {$alreadyOk}");
        $this->info("Fixed: {$fixed}");
        $this->info("Missing crypto_tx: {$missing}");

        if ($isDryRun && $fixed > 0) {
            $this->newLine();
            $this->warn("This was a DRY RUN. Run without --dry-run to apply changes.");
        }

        return 0;
    }
}
