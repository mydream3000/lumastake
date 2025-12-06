<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Устанавливаем active = true для пользователей, у которых есть хотя бы один подтвержденный депозит
        DB::statement("
            UPDATE users
            SET active = TRUE
            WHERE id IN (
                SELECT DISTINCT user_id
                FROM transactions
                WHERE type = 'deposit'
                AND status = 'confirmed'
            )
        ");

        // Устанавливаем active = false для всех остальных пользователей (без депозитов)
        DB::statement("
            UPDATE users
            SET active = FALSE
            WHERE id NOT IN (
                SELECT DISTINCT user_id
                FROM transactions
                WHERE type = 'deposit'
                AND status = 'confirmed'
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Откатываем изменения - устанавливаем всем active = true (как было по умолчанию)
        DB::statement("UPDATE users SET active = TRUE");
    }
};

