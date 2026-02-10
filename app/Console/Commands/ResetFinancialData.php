<?php

namespace App\Console\Commands;

use App\Models\CryptoTransaction;
use App\Models\Earning;
use App\Models\StakingDeposit;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetFinancialData extends Command
{
    protected $signature = 'app:reset-financial-data {--force : Skip confirmation}';
    protected $description = 'Reset all financial data (balances, transactions, staking, earnings) for project launch';

    public function handle(): int
    {
        $this->warn('⚠️  This will reset ALL financial data:');
        $this->line('  - All staking deposits → cancelled');
        $this->line('  - All transactions → deleted');
        $this->line('  - All crypto transactions → kept (as duplicate markers)');
        $this->line('  - All earnings → deleted');
        $this->line('  - All pending withdrawals → deleted');
        $this->line('  - All promo code usages → deleted (codes kept)');
        $this->line('  - All toast messages → deleted');
        $this->line('  - All user balances → 0');
        $this->line('  - All user deposited → 0');
        $this->line('  - All users deactivated (active = false)');
        $this->line('');
        $this->line('  ✅ Users, crypto addresses, tiers, promo codes, closer notes — NOT touched');

        if (!$this->option('force') && !$this->confirm('Are you sure you want to proceed?')) {
            $this->info('Cancelled.');
            return 0;
        }

        // Stats before
        $activeStakes = StakingDeposit::where('status', 'active')->count();
        $totalTransactions = Transaction::count();
        $totalEarnings = Earning::count();
        $totalCryptoTx = CryptoTransaction::count();
        $totalBalance = User::sum('balance');

        $this->info("📊 Current state:");
        $this->line("  Active stakes: {$activeStakes}");
        $this->line("  Transactions: {$totalTransactions}");
        $this->line("  Earnings: {$totalEarnings}");
        $this->line("  Crypto transactions: {$totalCryptoTx}");
        $this->line("  Total balance: \${$totalBalance}");
        $this->line('');

        DB::transaction(function () {
            // 1. Cancel all staking deposits
            $updated = StakingDeposit::whereIn('status', ['active', 'pending'])
                ->update(['status' => 'cancelled']);
            $this->line("  ✅ Staking deposits cancelled: {$updated}");

            // 2. Delete earnings
            $deleted = Earning::query()->delete();
            $this->line("  ✅ Earnings deleted: {$deleted}");

            // 3. Delete transactions
            $deleted = Transaction::query()->delete();
            $this->line("  ✅ Transactions deleted: {$deleted}");

            // 4. Keep crypto transactions as duplicate markers (prevent re-processing from blockchain)
            $count = CryptoTransaction::count();
            $this->line("  ✅ Crypto transactions kept: {$count} (prevents re-crediting from blockchain)");

            // 5. Delete pending withdrawals
            $deleted = DB::table('pending_withdrawals')->delete();
            $this->line("  ✅ Pending withdrawals deleted: {$deleted}");

            // 6. Reset promo code usages (keep codes)
            $deleted = DB::table('promo_code_usages')->delete();
            DB::table('promo_codes')->update(['used_count' => 0]);
            $this->line("  ✅ Promo code usages reset: {$deleted}");

            // 7. Delete toast messages
            $deleted = DB::table('toast_messages')->delete();
            $this->line("  ✅ Toast messages deleted: {$deleted}");

            // 8. Reset user balances
            User::query()->update([
                'balance' => 0,
                'deposited' => 0,
                'active' => false,
                'current_tier' => 1,
            ]);
            $this->line("  ✅ User balances reset to 0");
        });

        $this->newLine();
        $this->info('🎉 All financial data has been reset. Ready for launch!');

        return 0;
    }
}
