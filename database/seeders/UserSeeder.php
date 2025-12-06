<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Создаём основного администратора
        User::updateOrCreate(
            ['email' => 'admin@lumastake.com'], // Изменено на lumastake.com
            [
                'name' => 'Admin',
                'password' => 'password!',
                'is_admin' => true,
                'email_verified_at' => now(),
                'balance' => 10000.00,
                'active' => true,
            ]
        );
    }
}
