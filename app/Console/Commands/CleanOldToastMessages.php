<?php

namespace App\Console\Commands;

use App\Models\ToastMessage;
use Illuminate\Console\Command;

/**
 * Команда очистки старых toast-сообщений
 *
 * Удаляет уведомления (toast messages) старше 30 дней для поддержания чистоты БД.
 * Toast-сообщения используются для отображения уведомлений пользователям в личном кабинете
 * (успешные операции, ошибки, предупреждения). После просмотра они теряют актуальность.
 *
 * Рекомендуется запускать через планировщик задач (scheduler) ежедневно или еженедельно.
 * Это предотвращает бесконечный рост таблицы toast_messages.
 *
 * Период хранения (30 дней) можно изменить в теле команды при необходимости.
 */
class CleanOldToastMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'toast:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean toast messages older than 30 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deletedCount = ToastMessage::where('created_at', '<', now()->subDays(30))->delete();

        $this->info("Deleted {$deletedCount} old toast messages.");

        return 0;
    }
}
