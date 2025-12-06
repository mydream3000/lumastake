<?php

namespace Database\Seeders;

use App\Models\BotSetting;
use Illuminate\Database\Seeder;

class BotSettingSeeder extends Seeder
{
    public function run(): void
    {
        BotSetting::updateOrCreate(
            ['bot_token' => '7989635246:AAFanMGPRAU4Yis31JXQ5jYouEEK9q9q2hM'],
            [
                'bot_name' => 'Finace_lumastake_Bot',
                'chat_id' => '-1003101129553',
                'is_active' => true,
            ]
        );
    }
}
