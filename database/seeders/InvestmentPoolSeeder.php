<?php

namespace Database\Seeders;

use App\Models\InvestmentPool;
use Illuminate\Database\Seeder;

class InvestmentPoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiers = \App\Models\Tier::orderBy('level')->get();

        // Percentages for each tier by duration (days)
        $percentages = [
            1 => [10 => 2.5, 30 => 9, 60 => 20, 90 => 33, 180 => 70],            // Starter
            2 => [10 => 3.0, 30 => 10, 60 => 22, 90 => 36, 180 => 75],           // Bronze
            3 => [10 => 3.5, 30 => 11, 60 => 24, 90 => 38, 180 => 80],           // Silver
            4 => [10 => 4.0, 30 => 12, 60 => 26, 90 => 40, 180 => 85],           // Gold
            5 => [10 => 4.5, 30 => 13, 60 => 28, 90 => 42, 180 => 90],           // Platinum
            6 => [10 => 5.0, 30 => 14, 60 => 30, 90 => 45, 180 => 95],           // Titanium
            7 => [10 => 5.5, 30 => 15, 60 => 32, 90 => 48, 180 => 100],          // Diamond
            8 => [10 => 6.0, 30 => 16, 60 => 35, 90 => 50, 180 => 110],          // Elite Diamond
            9 => [10 => 6.5, 30 => 17, 60 => 37, 90 => 52, 180 => 120],          // Crown Elite
            10 => [10 => 7.0, 30 => 18, 60 => 40, 90 => 55, 180 => 130],         // Royal Legacy
        ];

        $durations = [10, 30, 60, 90, 180];
        $order = 1;

        foreach ($tiers as $tier) {
            foreach ($durations as $days) {
                InvestmentPool::updateOrCreate(
                    ['tier_id' => $tier->id, 'days' => $days],
                    [
                        'name' => "{$days} Days - {$tier->name}",
                        'min_stake' => 50.00,
                        'percentage' => $percentages[$tier->level][$days],
                        'is_active' => true,
                        'order' => $order++,
                    ]
                );
            }
        }
    }
}
