<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Добавляем колонку только если её ещё нет (безопасно для повторных прогонов)
            if (!Schema::hasColumn('users', 'withdrawal_blocked')) {
                $table->boolean('withdrawal_blocked')->default(false)->after('blocked');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Удаляем колонку только если она существует
            if (Schema::hasColumn('users', 'withdrawal_blocked')) {
                $table->dropColumn('withdrawal_blocked');
            }
        });
    }
};
