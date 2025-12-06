<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvestmentPoolsIslamicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('investment_pools_islamic')->truncate();

        $durations = [10, 30, 60, 90, 180];
        $tiersData = [
            // Tier 1: Starter
            1 => [[1.8, 2.2], [6.6, 8.3], [16, 20], [27, 33], [63, 77]],
            // Tier 2: Bronze
            2 => [[2, 2.4], [7.5, 9.1], [17.8, 21.8], [30, 36.3], [69.3, 84.7]],
            // Tier 3: Silver
            3 => [[2.2, 2.7], [8.2, 10], [19.6, 24], [32.7, 40], [76.3, 93.2]],
            // Tier 4: Gold
            4 => [[2.5, 3], [9, 11], [22, 26.5], [36, 44], [83.7, 102.3]],
            // Tier 5: Platinum
            5 => [[2.6, 3.2], [10, 12.1], [23.8, 29.1], [40, 48.4], [92, 112.5]],
            // Tier 6: Titanium
            6 => [[2.9, 3.5], [10.9, 13.3], [26.1, 31.9], [43.6, 53.3], [101.3, 123.8]],
            // Tier 7: Diamond
            7 => [[3.2, 3.9], [12, 14.7], [28.7, 35.1], [47.9, 58.5], [111.4, 136.2]],
            // Tier 8: Elite diamond
            8 => [[3.5, 4.3], [13.2, 16.1], [31.5, 38.5], [52.7, 64.4], [122.6, 149.9]],
            // Tier 9: Crown Elite
            9 => [[3.9, 4.8], [14.4, 17.6], [34.7, 42.4], [58, 70.9], [135, 165]],
            // Tier 10: Royal Legacy
            10 => [[4.4, 5.3], [15.9, 19.4], [38.2, 46.7], [64, 78.1], [148.5, 182]],
        ];

        $pools = [];
        foreach ($tiersData as $tierId => $percentages) {
            foreach ($durations as $index => $duration) {
                $pools[] = [
                    'tier_id' => $tierId,
                    'duration_days' => $duration,
                    'min_percentage' => $percentages[$index][0],
                    'max_percentage' => $percentages[$index][1],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('investment_pools_islamic')->insert($pools);
    }
}
