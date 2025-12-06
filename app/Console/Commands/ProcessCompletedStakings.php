<?php

namespace App\Console\Commands;

use App\Jobs\ProcessStakingCompletionJob;
use App\Models\StakingDeposit;
use Illuminate\Console\Command;

/**
 * Команда обработки завершенных стейкинг-депозитов
 *
 * Находит все активные стейкинги с наступившей датой окончания (end_date <= now)
 * и ставит их в очередь для обработки через ProcessStakingCompletionJob.
 *
 * Что происходит при обработке завершенного стейкинга:
 * - Начисление профита на баланс пользователя
 * - Возврат тела депозита на баланс
 * - Обработка реферальных начислений
 * - Создание записи о заработке
 * - Отправка уведомления пользователю
 * - Автопродление (если включено auto_renewal)
 *
 * Рекомендуется запускать через scheduler каждые несколько минут для своевременной
 * обработки завершенных стейкингов.
 */
class ProcessCompletedStakings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'staking:process-completed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process all completed staking deposits';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing completed stakings...');

        // Находим все активные стейкинги, у которых прошел срок
        $completedStakings = StakingDeposit::where('status', 'active')
            ->where('end_date', '<=', now())
            ->get();

        if ($completedStakings->isEmpty()) {
            $this->info('No completed stakings found.');
            return 0;
        }

        $this->info("Found {$completedStakings->count()} completed staking(s).");

        $bar = $this->output->createProgressBar($completedStakings->count());
        $bar->start();

        foreach ($completedStakings as $staking) {
            ProcessStakingCompletionJob::dispatch($staking->id);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('All completed stakings have been queued for processing.');

        return 0;
    }
}
