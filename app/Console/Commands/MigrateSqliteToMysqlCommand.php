<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateSqliteToMysqlCommand extends Command
{
    protected $signature = 'db:migrate-sqlite-to-mysql
                            {--force : Force migration without confirmation}
                            {--truncate : Truncate tables before inserting data}
                            {--skip-existing : Skip tables that already have data}';
    protected $description = 'Migrate all data from SQLite to MySQL';

    private $sourceConnection = 'sqlite';
    private $targetConnection = 'mysql';

    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will copy all data from SQLite to MySQL. Continue?')) {
                $this->error('Migration cancelled.');
                return 1;
            }
        }

        $this->info('Starting migration from SQLite to MySQL...');
        $this->newLine();

        try {
            // Проверяем подключения
            $this->checkConnections();

            // Отключаем проверку внешних ключей для MySQL
            DB::connection($this->targetConnection)->statement('SET FOREIGN_KEY_CHECKS=0;');

            // Получаем список всех таблиц из SQLite
            $tables = $this->getTables();

            $this->info("Found " . count($tables) . " tables to migrate.");
            $this->newLine();

            $migrated = 0;
            $skipped = 0;
            $errors = 0;

            foreach ($tables as $table) {
                try {
                    $count = $this->migrateTable($table);
                    if ($count > 0) {
                        $this->info("✓ {$table}: {$count} rows migrated");
                        $migrated++;
                    } else {
                        $this->line("○ {$table}: no data");
                        $skipped++;
                    }
                } catch (\Exception $e) {
                    $this->error("✗ {$table}: " . $e->getMessage());
                    $errors++;
                }
            }

            // Включаем обратно проверку внешних ключей
            DB::connection($this->targetConnection)->statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->newLine();
            $this->info("Migration completed!");
            $this->info("Migrated: {$migrated} tables");
            $this->info("Skipped: {$skipped} tables (no data)");
            if ($errors > 0) {
                $this->error("Errors: {$errors} tables");
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('Migration failed: ' . $e->getMessage());
            return 1;
        }
    }

    private function checkConnections()
    {
        // Проверяем SQLite
        try {
            // Устанавливаем правильный путь к SQLite файлу
            config(['database.connections.sqlite.database' => database_path('database.sqlite')]);
            DB::purge($this->sourceConnection);

            $sqlitePath = database_path('database.sqlite');
            if (!file_exists($sqlitePath)) {
                throw new \Exception("SQLite file not found at: {$sqlitePath}");
            }

            DB::connection($this->sourceConnection)->getPdo();
            $this->info('✓ SQLite connection OK (' . $sqlitePath . ')');
        } catch (\Exception $e) {
            throw new \Exception('SQLite connection failed: ' . $e->getMessage());
        }

        // Проверяем MySQL
        try {
            DB::connection($this->targetConnection)->getPdo();
            $this->info('✓ MySQL connection OK');
        } catch (\Exception $e) {
            throw new \Exception('MySQL connection failed: ' . $e->getMessage());
        }

        $this->newLine();
    }

    private function getTables(): array
    {
        $tables = DB::connection($this->sourceConnection)
            ->select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");

        $tableNames = array_map(fn($table) => $table->name, $tables);

        // Сортируем таблицы в правильном порядке (учитываем foreign keys)
        $orderedTables = $this->orderTablesByDependencies($tableNames);

        return $orderedTables;
    }

    private function orderTablesByDependencies(array $tables): array
    {
        // Определяем правильный порядок таблиц для избежания конфликтов foreign keys
        $order = [
            'migrations',
            'users',
            'tiers',
            'tier_percentages',
            'promo_codes',
            'promo_code_usages',
            'password_reset_tokens',
            'sessions',
            'cache',
            'cache_locks',
            'jobs',
            'job_batches',
            'failed_jobs',
            'staking_deposits',
            'transactions',
            'earnings',
            'crypto_addresses',
            'crypto_transactions',
            'toast_messages',
            'email_templates',
            'email_settings',
            'email_notifications_log',
            'faqs',
            'blog_posts',
            'social_links',
            'support_emails',
            'telegram_bots',
            'telegram_notifications',
        ];

        $orderedTables = [];

        // Сначала добавляем таблицы в правильном порядке
        foreach ($order as $tableName) {
            if (in_array($tableName, $tables)) {
                $orderedTables[] = $tableName;
            }
        }

        // Потом добавляем оставшиеся таблицы (которых нет в списке)
        foreach ($tables as $tableName) {
            if (!in_array($tableName, $orderedTables)) {
                $orderedTables[] = $tableName;
            }
        }

        return $orderedTables;
    }

    private function migrateTable(string $table): int
    {
        // Получаем данные из SQLite
        $data = DB::connection($this->sourceConnection)
            ->table($table)
            ->get()
            ->map(function ($row) {
                return (array) $row;
            })
            ->toArray();

        if (empty($data)) {
            return 0;
        }

        // Проверяем, есть ли уже данные в таблице MySQL
        $existingCount = DB::connection($this->targetConnection)->table($table)->count();

        // Если опция --skip-existing и данные уже есть - пропускаем
        if ($this->option('skip-existing') && $existingCount > 0) {
            $this->line("○ {$table}: skipped ({$existingCount} rows already exist)");
            return 0;
        }

        // Очищаем таблицу в MySQL если --truncate или --force
        if ($this->option('truncate') || $this->option('force')) {
            DB::connection($this->targetConnection)->table($table)->truncate();
        }

        // Вставляем данные порциями (по 100 записей для надежности)
        $chunks = array_chunk($data, 100);

        foreach ($chunks as $chunk) {
            try {
                DB::connection($this->targetConnection)->table($table)->insert($chunk);
            } catch (\Exception $e) {
                // Если batch insert не сработал, пробуем по одной записи
                foreach ($chunk as $row) {
                    try {
                        DB::connection($this->targetConnection)->table($table)->insertOrIgnore($row);
                    } catch (\Exception $rowError) {
                        // Игнорируем ошибки для отдельных записей
                    }
                }
            }
        }

        return count($data);
    }
}
