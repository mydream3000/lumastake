<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            TestDataSeeder::class, // Добавляем сидер с тестовыми данными
            TierSeeder::class,
            InvestmentPoolSeeder::class,
            CountrySeeder::class,
            FaqSeeder::class,
            BlogSeeder::class,
            ReferralLevelSeeder::class,
            BotSettingSeeder::class,
            SupportTeamSeeder::class,
            SocialLinksSeeder::class,
            EmailTemplatesSeeder::class,
            TierPercentagesIslamicSeeder::class,
            InvestmentPoolsIslamicSeeder::class,
        ]);
    }
}
