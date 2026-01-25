<?php

namespace App\Console\Commands;

use App\Mail\TemplatedMail;
use App\Models\EmailNotificationLog;
use App\Models\StakingDeposit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendStakingExpiringReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'staking:send-expiring-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders for stakings expiring within 24 hours';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tomorrow = now()->addDay()->startOfDay();
        $tomorrowEnd = now()->addDay()->endOfDay();

        // Find all active stakings ending tomorrow
        $stakings = StakingDeposit::with('user')
            ->where('status', 'active')
            ->whereBetween('end_date', [$tomorrow, $tomorrowEnd])
            ->get();

        $sent = 0;
        $skipped = 0;

        foreach ($stakings as $staking) {
            // Check if reminder was already sent
            if (EmailNotificationLog::wasAlreadySent(
                'staking_expiring_soon',
                'staking_deposit',
                $staking->id
            )) {
                $skipped++;
                continue;
            }

            try {
                // Calculate expected profit
                $expectedProfit = round($staking->amount * $staking->percentage / 100, 2);

                // Send email using TemplatedMail (editable template from DB)
                Mail::to($staking->user->email)->send(new TemplatedMail(
                    'staking_expiring_soon',
                    [
                        'userName' => $staking->user->name,
                        'amount' => $staking->amount,
                        'days' => $staking->days,
                        'percentage' => $staking->percentage,
                        'profitAmount' => $expectedProfit,
                        'endDate' => $staking->end_date->format('M d, Y'),
                        'autoRenewal' => $staking->auto_renewal,
                    ],
                    $staking->user_id,
                    'staking_deposit',
                    $staking->id
                ));

                $sent++;
                $this->info("✓ Sent reminder to {$staking->user->email} for staking #{$staking->id}");
            } catch (\Exception $e) {
                Log::error('Failed to send staking expiring reminder', [
                    'staking_id' => $staking->id,
                    'user_id' => $staking->user_id,
                    'error' => $e->getMessage(),
                ]);
                $this->error("✗ Failed to send reminder for staking #{$staking->id}: {$e->getMessage()}");
            }
        }

        $this->info("\nSummary: Sent {$sent} reminders, skipped {$skipped} (already sent)");

        return Command::SUCCESS;
    }
}
