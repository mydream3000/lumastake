<?php

namespace App\Console\Commands;

use App\Models\StakingDeposit;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Команда для ускорения завершения стейкингов (для тестирования)
 *
 * ВНИМАНИЕ: Используйте ТОЛЬКО в dev/test окружении!
 *
 * Примеры использования:
 * php artisan staking:accelerate --user=28 --hours=2
 * php artisan staking:accelerate --user=28 --hours=0.5  (30 минут)
 * php artisan staking:accelerate --user=28 --minutes=30
 */
class AccelerateStakingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'staking:accelerate
                            {--user= : ID пользователя}
                            {--hours=2 : Через сколько часов завершатся стейкинги}
                            {--minutes= : Через сколько минут завершатся стейкинги (альтернатива --hours)}
                            {--force : Пропустить проверку окружения}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ускорить завершение стейкингов пользователя (для тестирования)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Проверка окружения (можно пропустить с --force)
        if (!$this->option('force') && app()->environment('production')) {
            $this->error('❌ ОШИБКА: Эта команда не должна использоваться в production окружении!');
            $this->error('   Если вы уверены, добавьте флаг --force');
            return 1;
        }

        // Получаем параметры
        $userId = $this->option('user');
        $hours = $this->option('hours');
        $minutes = $this->option('minutes');

        // Запрашиваем user_id если не передан
        if (!$userId) {
            $userId = $this->ask('Введите ID пользователя');
            if (!$userId) {
                $this->error('❌ ID пользователя обязателен!');
                return 1;
            }
        }

        // Вычисляем время
        if ($minutes) {
            $completionTime = now()->addMinutes((float) $minutes);
            $timeDescription = "{$minutes} минут(ы)";
        } else {
            $completionTime = now()->addHours((float) $hours);
            $timeDescription = "{$hours} час(а)";
        }

        $this->info("🚀 Ускорение стейкингов");
        $this->info("   Пользователь: ID {$userId}");
        $this->info("   Новое время завершения: через {$timeDescription}");
        $this->newLine();

        // Проверяем пользователя
        $user = User::find($userId);
        if (!$user) {
            $this->error("❌ Пользователь с ID {$userId} не найден!");
            return 1;
        }

        $this->info("✅ Пользователь: {$user->name} ({$user->email})");
        $this->newLine();

        // Получаем активные стейкинги
        $activeStakings = StakingDeposit::where('user_id', $userId)
            ->where('status', 'active')
            ->get();

        if ($activeStakings->isEmpty()) {
            $this->warn("⚠️  У пользователя нет активных стейкингов");
            return 0;
        }

        $this->info("📊 Найдено активных стейкингов: {$activeStakings->count()}");
        $this->newLine();

        // Показываем таблицу ДО изменений
        $this->table(
            ['ID', 'Сумма', 'Период', 'Текущая дата окончания', 'Осталось'],
            $activeStakings->map(function ($staking) {
                $remaining = $staking->end_date->diffForHumans(null, true);
                return [
                    $staking->id,
                    number_format($staking->amount, 2) . ' USDT',
                    $staking->days . ' дн.',
                    $staking->end_date->format('d.m.Y H:i:s'),
                    $remaining,
                ];
            })->toArray()
        );

        // Подтверждение
        if (!$this->option('force')) {
            if (!$this->confirm('Продолжить изменение дат?', true)) {
                $this->warn('Операция отменена');
                return 0;
            }
        }

        // Обновляем даты
        DB::transaction(function () use ($activeStakings, $completionTime) {
            foreach ($activeStakings as $staking) {
                $staking->end_date = $completionTime;
                $staking->save();
            }
        });

        $this->newLine();
        $this->info("✅ Успешно обновлено стейкингов: {$activeStakings->count()}");
        $this->info("⏰ Все стейкинги завершатся: {$completionTime->format('d.m.Y H:i:s')}");
        $this->info("⏱️  Это через: {$timeDescription} от текущего времени");
        $this->newLine();

        // Показываем таблицу ПОСЛЕ изменений
        $this->info("📋 Обновленные стейкинги:");
        $this->table(
            ['ID', 'Сумма', 'Период', 'Новая дата окончания', 'Осталось'],
            $activeStakings->map(function ($staking) use ($completionTime) {
                $remaining = $completionTime->diffForHumans(null, true);
                return [
                    $staking->id,
                    number_format($staking->amount, 2) . ' USDT',
                    $staking->days . ' дн.',
                    $completionTime->format('d.m.Y H:i:s'),
                    $remaining,
                ];
            })->toArray()
        );

        $this->newLine();
        $this->info("💡 Для обработки завершенных стейкингов запустите:");
        $this->info("   php artisan schedule:work");

        return 0;
    }
}
