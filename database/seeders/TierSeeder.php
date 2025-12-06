<?php

namespace Database\Seeders;

use App\Models\Tier;
use App\Models\TierPercentage;
use Illuminate\Database\Seeder;

class TierSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = [
            ['name' => 'Starter', 'color' => '#f6bd0e', 'level' => 1, 'min_balance' => 0, 'max_balance' => 999],
            ['name' => 'Bronze', 'color' => '#ff613e', 'level' => 2, 'min_balance' => 1000, 'max_balance' => 4999],
            ['name' => 'Silver', 'color' => '#f70808', 'level' => 3, 'min_balance' => 5000, 'max_balance' => 9999],
            ['name' => 'Gold', 'color' => '#e10495', 'level' => 4, 'min_balance' => 10000, 'max_balance' => 19999],
            ['name' => 'Platinum', 'color' => '#c000ca', 'level' => 5, 'min_balance' => 20000, 'max_balance' => 29999],
            ['name' => 'Titanium', 'color' => '#2f7dea', 'level' => 6, 'min_balance' => 30000, 'max_balance' => 44999],
            ['name' => 'Diamond', 'color' => '#1393af', 'level' => 7, 'min_balance' => 45000, 'max_balance' => 59999],
            ['name' => 'Elite Diamond', 'color' => '#29bdb4', 'level' => 8, 'min_balance' => 60000, 'max_balance' => 74999],
            ['name' => 'Crown Elite', 'color' => '#82ca8a', 'level' => 9, 'min_balance' => 75000, 'max_balance' => 99999],
            ['name' => 'Royal Legacy', 'color' => '#caca33', 'level' => 10, 'min_balance' => 100000, 'max_balance' => null],
        ];

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

        foreach ($tiers as $t) {
            $tier = Tier::query()->updateOrCreate(
                ['level' => $t['level']],
                [
                    'name' => $t['name'],
                    'color' => $t['color'],
                    'min_balance' => $t['min_balance'],
                    'max_balance' => $t['max_balance']
                ]
            );

            foreach ($percentages[$t['level']] as $days => $percentage) {
                TierPercentage::query()->updateOrCreate(
                    ['tier_id' => $tier->id, 'days' => $days],
                    ['percentage' => $percentage]
                );
            }
        }
    }
}
