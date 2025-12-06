<?php

namespace App\Console\Commands;

use App\Models\SocialLink;
use Illuminate\Console\Command;

class AddSocialLinksCommand extends Command
{
    protected $signature = 'social-links:add-youtube-telegram';
    protected $description = 'Add YouTube and Telegram social links';

    public function handle()
    {
        $platforms = ['YouTube', 'Telegram'];

        foreach ($platforms as $platform) {
            $exists = SocialLink::where('platform', $platform)->exists();

            if (!$exists) {
                SocialLink::create([
                    'platform' => $platform,
                    'url' => null,
                    'is_active' => false,
                ]);
                $this->info("Added {$platform}");
            } else {
                $this->info("{$platform} already exists");
            }
        }

        $this->info('Done!');
    }
}
