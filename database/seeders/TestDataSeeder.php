<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Создаём тестового администратора
        User::updateOrCreate(
            ['email' => 'test_admin@lumastake.com'],
            [
                'name' => 'Test Admin',
                'password' => 'password!',
                'is_admin' => true,
                'email_verified_at' => now(),
                'balance' => 10000.00,
                'active' => true,
            ]
        );

        // 2. Создаём обычного тестового пользователя
        $testUser = User::updateOrCreate(
            ['email' => 'test_user@lumastake.com'],
            [
                'name' => 'Test User',
                'password' => 'password!',
                'is_admin' => false,
                'email_verified_at' => now(),
                'balance' => 500.00,
                'active' => true,
                'referral_code' => 'TESTUSER',
            ]
        );

        // 3. Создаём реферала для test_user@lumastake.com
        User::updateOrCreate(
            ['email' => 'test_referral@lumastake.com'],
            [
                'name' => 'Test Referral',
                'password' => 'password!',
                'is_admin' => false,
                'email_verified_at' => now(),
                'balance' => 5000.00,
                'deposited' => 5000.00,
                'active' => true,
                'referred_by' => $testUser->id,
                'referral_code' => 'REFERRAL01',
            ]
        );

        // 4. Создаём несколько тестовых пользователей с разными балансами и тирами
        $testUsers = [
            [
                'name' => 'John Doe',
                'email' => 'john_test@example.com',
                'balance' => 50.00,
                'current_tier' => 1,
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane_test@example.com',
                'balance' => 1500.00,
                'current_tier' => 2,
            ],
        ];

        foreach ($testUsers as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => 'password!',
                    'is_admin' => false,
                    'email_verified_at' => now(),
                    'balance' => $userData['balance'],
                    'current_tier' => $userData['current_tier'],
                    'active' => true,
                    'referral_code' => Str::random(8),
                ]
            );
        }
    }
}
