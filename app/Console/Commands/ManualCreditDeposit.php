<?php

namespace App\Console\Commands;

use App\Jobs\ProcessDepositJob;
use App\Models\User;
use Illuminate\Console\Command;

class ManualCreditDeposit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crypto:credit-deposit
                            {user_id : User ID to credit}
                            {amount : Amount to credit}
                            {--tx-hash= : Transaction hash (optional)}
                            {--network=tron : Network (tron, ethereum, bsc)}
                            {--token=USDT : Token symbol}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually credit a deposit to user account (emergency use only)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $amount = (float) $this->argument('amount');
        $txHash = $this->option('tx-hash') ?: 'manual-' . time() . '-' . $userId;
        $network = $this->option('network');
        $token = $this->option('token');

        // Проверяем пользователя
        $user = User::find($userId);
        if (!$user) {
            $this->error("User #{$userId} not found!");
            return 1;
        }

        $this->info("📝 Manual Deposit Credit");
        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->line("User:    #{$userId} ({$user->name})");
        $this->line("Email:   {$user->email}");
        $this->line("Amount:  {$amount} {$token}");
        $this->line("Network: {$network}");
        $this->line("TX Hash: {$txHash}");
        $this->line("Current Balance: {$user->balance}");
        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

        if (!$this->confirm('Do you want to credit this deposit?', true)) {
            $this->warn('Cancelled.');
            return 0;
        }

        try {
            // Запускаем Job для обработки депозита
            ProcessDepositJob::dispatch($userId, $amount, $txHash, $network, $token);

            $this->info("✅ Deposit credited successfully!");
            $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

            // Обновляем данные пользователя
            $user->refresh();
            $this->line("New Balance: {$user->balance}");

            $this->info("✅ Job dispatched. Check queue:work output for processing status.");

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Failed to credit deposit: " . $e->getMessage());
            return 1;
        }
    }
}
