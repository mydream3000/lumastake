<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewTestDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Создаём тестового администратора
        User::updateOrCreate(
            ['email' => 'new_test_admin@lumastake.com'],
            [
                'name' => 'New Test Admin',
                'password' => 'password!',
                'is_admin' => true,
                'email_verified_at' => now(),
                'balance' => 15000.00,
                'active' => true,
            ]
        );

        // 2. Создаём второго тестового администратора
        User::updateOrCreate(
            ['email' => 'another_test_admin@lumastake.com'],
            [
                'name' => 'Another Test Admin',
                'password' => 'password!',
                'is_admin' => true,
                'email_verified_at' => now(),
                'balance' => 20000.00,
                'active' => true,
            ]
        );

        // 3. Создаём нового обычного тестового пользователя
        $newTestUser = User::updateOrCreate(
            ['email' => 'new_test_user@lumastake.com'],
            [
                'name' => 'New Test User',
                'password' => 'password!',
                'is_admin' => false,
                'email_verified_at' => now(),
                'balance' => 1000.00,
                'active' => true,
                'referral_code' => 'NEWTESTUSER',
            ]
        );

        // 4. Создаём реферала для new_test_user@lumastake.com
        User::updateOrCreate(
            ['email' => 'new_test_referral@lumastake.com'],
            [
                'name' => 'New Test Referral',
                'password' => 'password!',
                'is_admin' => false,
                'email_verified_at' => now(),
                'balance' => 7500.00,
                'deposited' => 7500.00,
                'active' => true,
                'referred_by' => $newTestUser->id,
                'referral_code' => 'NEWREFERRAL01',
            ]
        );

        // 5. Создаём несколько новых тестовых пользователей
        $newTestUsers = [
            [
                'name' => 'Alice Johnson',
                'email' => 'alice_test@example.com',
                'balance' => 250.00,
                'current_tier' => 1,
            ],
            [
                'name' => 'Bob Williams',
                'email' => 'bob_test@example.com',
                'balance' => 3000.00,
                'current_tier' => 2,
            ],
            [
                'name' => 'Charlie Brown',
                'email' => 'charlie_test@example.com',
                'balance' => 10000.00,
                'current_tier' => 4,
            ],
        ];

        foreach ($newTestUsers as $userData) {
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
