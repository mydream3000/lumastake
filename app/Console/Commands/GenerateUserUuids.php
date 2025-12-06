<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Команда для генерации UUID для пользователей
 *
 * Используется для заполнения поля uuid у существующих пользователей, которые были созданы
 * до введения обязательного UUID. UUID используется для публичных ссылок (например, реферальные ссылки),
 * чтобы не раскрывать внутренние ID пользователей.
 *
 * Применение:
 * - Разовая миграция данных при добавлении поля uuid в таблицу users
 * - Восстановление отсутствующих UUID после импорта данных
 *
 * Команда безопасна для повторного запуска - обрабатывает только пользователей без UUID.
 */
class GenerateUserUuids extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-user-uuids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate UUIDs for existing users without one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::whereNull('uuid')->get();

        if ($users->isEmpty()) {
            $this->info('All users already have UUIDs.');
            return 0;
        }

        $count = 0;
        foreach ($users as $user) {
            $user->uuid = (string) Str::uuid();
            $user->save();
            $count++;
        }

        $this->info("Generated UUIDs for {$count} user(s).");
        return 0;
    }
}
