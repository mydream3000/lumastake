<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tier_percentages', function (Blueprint $table) {
            if (!Schema::hasColumn('tier_percentages', 'min_stake')) {
                $table->decimal('min_stake', 10, 2)->default(50.00)->after('percentage');
            }
            if (!Schema::hasColumn('tier_percentages', 'order')) {
                $table->unsignedInteger('order')->nullable()->after('min_stake');
            }
        });

        // Точечное наполнение min_stake и order из investment_pools при наличии соответствий
        if (Schema::hasTable('investment_pools')) {
            // Для каждой записи tier_percentages берём совпадающую по (tier_id, days)
            $sql = "
                UPDATE tier_percentages tp
                JOIN investment_pools ip
                  ON ip.tier_id = tp.tier_id
                 AND ip.days = tp.days
                SET tp.min_stake = COALESCE(ip.min_stake, tp.min_stake),
                    tp.`order`   = COALESCE(ip.`order`, tp.`order`)
            ";

            try {
                DB::statement($sql);
            } catch (\Throwable $e) {
                // В некоторых СУБД (например, SQLite для тестов) синтаксис JOIN в UPDATE недоступен
                // В таком случае просто оставим значения по умолчанию
            }
        }

        // Индекс по days для быстрых выборок (без Doctrine DBAL)
        try {
            // Проверяем наличие индекса в MySQL
            $hasIndex = false;
            try {
                $rows = DB::select("SHOW INDEXES FROM tier_percentages WHERE Key_name = 'tier_percentages_days_index'");
                $hasIndex = !empty($rows);
            } catch (\Throwable $e) {
                // На SQLite/других СУБД команда может отсутствовать — создадим индекс вслепую ниже
            }

            if (!$hasIndex) {
                Schema::table('tier_percentages', function (Blueprint $table) {
                    $table->index('days');
                });
            }
        } catch (\Throwable $e) {
            // В случае ошибок при проверке — пробуем создать индекс без проверки
            try {
                Schema::table('tier_percentages', function (Blueprint $table) {
                    $table->index('days');
                });
            } catch (\Throwable $ignored) {
                // Если индекс уже существует или СУБД не поддерживает — молча пропускаем
            }
        }
    }

    public function down(): void
    {
        Schema::table('tier_percentages', function (Blueprint $table) {
            if (Schema::hasColumn('tier_percentages', 'order')) {
                $table->dropColumn('order');
            }
            if (Schema::hasColumn('tier_percentages', 'min_stake')) {
                $table->dropColumn('min_stake');
            }
        });
    }
};
