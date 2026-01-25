<?php

namespace Database\Seeders;

use App\Models\SocialLink;
use Illuminate\Database\Seeder;

class SocialLinksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $platforms = ['Instagram', 'Facebook', 'Twitter', 'TikTok', 'YouTube', 'Telegram'];

        foreach ($platforms as $platform) {
            SocialLink::updateOrCreate(
                ['platform' => $platform],
                [
                    'url' => null,
                    'is_active' => false,
                ]
            );
        }
    }
}
