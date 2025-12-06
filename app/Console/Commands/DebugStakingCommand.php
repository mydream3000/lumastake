<?php

namespace App\Console\Commands;

use App\Models\StakingDeposit;
use App\Models\User;
use Illuminate\Console\Command;

class DebugStakingCommand extends Command
{
    protected $signature = 'debug:staking {--user=28}';
    protected $description = 'Debug staking time calculations';

    public function handle()
    {
        $userId = $this->option('user');
        $user = User::find($userId);

        if (!$user) {
            $this->error("User {$userId} not found");
            return 1;
        }

        $this->info("User: {$user->name} ({$user->email})");
        $this->newLine();

        $stakes = $user->stakingDeposits()->where('status', 'active')->get();

        if ($stakes->isEmpty()) {
            $this->warn('No active stakes');
            return 0;
        }

        $this->info("Server time: " . now());
        $this->info("Server timezone: " . config('app.timezone'));
        $this->newLine();

        $this->table(
            ['ID', 'Amount', 'Days', 'Start Date', 'End Date', 'Time Left (seconds)', 'Human Readable'],
            $stakes->map(function ($stake) {
                $now = now();
                $timeLeftSeconds = $stake->end_date->timestamp - $now->timestamp;

                return [
                    $stake->id,
                    '$' . number_format($stake->amount, 2),
                    $stake->days,
                    $stake->start_date->format('Y-m-d H:i:s'),
                    $stake->end_date->format('Y-m-d H:i:s'),
                    $timeLeftSeconds,
                    $stake->end_date->diffForHumans(),
                ];
            })->toArray()
        );

        $this->newLine();
        $this->info("API Response for first stake:");
        $stake = $stakes->first();
        $now = now();
        $timeLeftSeconds = $stake->end_date->timestamp - $now->timestamp;

        $this->line("  end_date timestamp: " . $stake->end_date->timestamp);
        $this->line("  now timestamp:      " . $now->timestamp);
        $this->line("  difference:         " . $timeLeftSeconds . " seconds");
        $this->line("  in hours:           " . round($timeLeftSeconds / 3600, 2) . " hours");

        return 0;
    }
}
