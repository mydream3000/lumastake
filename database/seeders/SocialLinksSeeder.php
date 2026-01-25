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

        // Footer links (for footer + blog sharing)
        foreach ($platforms as $platform) {
            SocialLink::updateOrCreate(
                ['platform' => $platform, 'type' => SocialLink::TYPE_FOOTER],
                [
                    'url' => null,
                    'is_active' => false,
                ]
            );
        }

        // Cabinet links (for contact form in cabinet)
        foreach ($platforms as $platform) {
            SocialLink::updateOrCreate(
                ['platform' => $platform, 'type' => SocialLink::TYPE_CABINET],
                [
                    'url' => null,
                    'is_active' => false,
                ]
            );
        }
    }
}
