<?php

namespace App\Console\Commands;

use App\Models\Earning;
use App\Models\StakingDeposit;
use Illuminate\Console\Command;

class UpdateEarningsTierName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'earnings:update-tier-name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update existing profit earnings with tier_name from related staking deposits';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to update earnings with tier_name...');

        // Получаем все earnings типа 'profit', у которых нет tier_name в meta
        $earnings = Earning::where('type', 'profit')
            ->get()
            ->filter(function ($earning) {
                return !isset($earning->meta['tier_name']) && isset($earning->meta['staking_deposit_id']);
            });

        if ($earnings->isEmpty()) {
            $this->info('No earnings to update.');
            return 0;
        }

        $this->info("Found {$earnings->count()} earnings to update.");

        $bar = $this->output->createProgressBar($earnings->count());
        $bar->start();

        $updated = 0;
        $failed = 0;

        foreach ($earnings as $earning) {
            try {
                $stakingDeposit = StakingDeposit::find($earning->meta['staking_deposit_id']);

                if ($stakingDeposit && $stakingDeposit->tier) {
                    $meta = $earning->meta;
                    $meta['tier_name'] = $stakingDeposit->tier->name;

                    $earning->meta = $meta;
                    $earning->save();

                    $updated++;
                } else {
                    $failed++;
                    $this->newLine();
                    $this->warn("Could not find staking deposit or tier for earning ID: {$earning->id}");
                }
            } catch (\Exception $e) {
                $failed++;
                $this->newLine();
                $this->error("Error updating earning ID {$earning->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Update completed!");
        $this->info("Successfully updated: {$updated}");

        if ($failed > 0) {
            $this->warn("Failed to update: {$failed}");
        }

        return 0;
    }
}
