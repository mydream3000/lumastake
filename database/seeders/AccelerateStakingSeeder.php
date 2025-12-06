<?php

namespace Database\Seeders;

use App\Models\StakingDeposit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Универсальный сидер для ускорения окончания стейкингов (для тестирования)
 *
 * БЕЗОПАСНОСТЬ: В production окружении запрашивает подтверждение
 *
 * Использование:
 * php artisan db:seed --class=AccelerateStakingSeeder --force
 *
 * Для автоматического подтверждения добавьте в .env:
 * ALLOW_STAKING_ACCELERATION=true
 *
 * Параметры настройки (изменить в методе run()):
 * - $userId - ID пользователя (по умолчанию 28)
 * - $hoursUntilCompletion - через сколько часов закончатся стейкинги (по умолчанию 4)
 */
class AccelerateStakingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ⚠️ БЕЗОПАСНОСТЬ: Предупреждение для production
        if (app()->environment('production')) {
            $this->command->warn('⚠️  ВНИМАНИЕ: Вы работаете в PRODUCTION окружении!');
            $this->command->warn('   Эта команда изменит даты стейкингов в реальной базе данных.');

            // Проверяем переменную окружения для автоматического подтверждения
            if (!env('ALLOW_STAKING_ACCELERATION', false)) {
                $this->command->newLine();
                if (!$this->command->confirm('Вы уверены, что хотите продолжить?', false)) {
                    $this->command->error('❌ Операция отменена');
                    return;
                }
            }
        }

        // ========================================
        // 🔧 ПАРАМЕТРЫ (измените под ваши нужды)
        // ========================================
        $userId = 28;  // ID пользователя
        $hoursUntilCompletion = 1;  // Через сколько часов закончатся стейкинги

        $this->command->info("🚀 Запуск AccelerateStakingSeeder");
        $this->command->info("   Пользователь: ID {$userId}");
        $this->command->info("   Новое время окончания: через {$hoursUntilCompletion} час(а)");
        $this->command->newLine();

        // Проверяем существование пользователя
        $user = User::find($userId);
        if (!$user) {
            $this->command->error("❌ Пользователь с ID {$userId} не найден!");
            return;
        }

        $this->command->info("✅ Пользователь найден: {$user->name} ({$user->email})");
        $this->command->newLine();

        // Получаем все активные стейкинги пользователя
        $activeStakings = StakingDeposit::where('user_id', $userId)
            ->where('status', 'active')
            ->get();

        if ($activeStakings->isEmpty()) {
            $this->command->warn("⚠️  У пользователя нет активных стейкингов");
            return;
        }

        $this->command->info("📊 Найдено активных стейкингов: {$activeStakings->count()}");
        $this->command->newLine();

        // Новая дата окончания
        $newEndDate = now()->addHours($hoursUntilCompletion);

        DB::transaction(function () use ($activeStakings, $newEndDate) {
            $this->command->table(
                ['ID', 'Сумма', 'Дней', 'Старая дата окончания', 'Новая дата окончания'],
                $activeStakings->map(function ($staking) use ($newEndDate) {
                    $oldEndDate = $staking->end_date;

                    // Обновляем дату окончания
                    $staking->end_date = $newEndDate;
                    $staking->save();

                    return [
                        $staking->id,
                        number_format($staking->amount, 2) . ' USDT',
                        $staking->days . ' дн.',
                        $oldEndDate->format('d.m.Y H:i:s'),
                        $newEndDate->format('d.m.Y H:i:s'),
                    ];
                })->toArray()
            );
        });

        $this->command->newLine();
        $this->command->info("✅ Успешно обновлено стейкингов: {$activeStakings->count()}");
        $this->command->info("⏰ Все стейкинги завершатся: {$newEndDate->format('d.m.Y H:i:s')}");
        $this->command->info("⏱️  Это через: {$hoursUntilCompletion} час(а) от текущего времени");
        $this->command->newLine();
        $this->command->info("💡 Для обработки завершенных стейкингов запустите:");
        $this->command->info("   php artisan schedule:work");
    }
}
