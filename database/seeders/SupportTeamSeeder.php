<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SupportEmail;
use App\Models\TelegramBot;

class SupportTeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создать email-адреса для поддержки
        SupportEmail::updateOrCreate(
            ['email' => 'inquiries@lumastake.com'],
            [
                'name' => 'General Inquiries',
                'is_active' => true,
            ]
        );

        SupportEmail::updateOrCreate(
            ['email' => 'support@lumastake.com'],
            [
                'name' => 'Technical Support',
                'is_active' => true,
            ]
        );

        // Создать Telegram бота
        TelegramBot::updateOrCreate(
            ['name' => 'Lumastake Info Bot'],
            [
                'bot_token' => '8500874442:AAEEBx4-z32wzlb70UoqZT4pdkYFCfy2r-A',
                'chat_id' => '', // Нужно заполнить в админке
                'is_active' => false, // Деактивирован до заполнения chat_id
            ]
        );
    }
}
