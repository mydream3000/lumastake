<?php

namespace Database\Seeders;

use App\Models\ReferralLevel;
use Illuminate\Database\Seeder;

class ReferralLevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            [
                'level' => 1,
                'name' => '1–5',
                'min_partners' => 1,
                'reward_percentage' => 10.00,
            ],
            [
                'level' => 2,
                'name' => '6–10',
                'min_partners' => 6,
                'reward_percentage' => 12.00,
            ],
            [
                'level' => 3,
                'name' => '11–15',
                'min_partners' => 11,
                'reward_percentage' => 15.00,
            ],
            [
                'level' => 4,
                'name' => '16–20',
                'min_partners' => 16,
                'reward_percentage' => 17.00,
            ],
            [
                'level' => 5,
                'name' => '21 & Above',
                'min_partners' => 21,
                'reward_percentage' => 20.00,
            ],
        ];

        foreach ($levels as $level) {
            ReferralLevel::updateOrCreate(
                ['level' => $level['level']],
                $level
            );
        }
    }
}
