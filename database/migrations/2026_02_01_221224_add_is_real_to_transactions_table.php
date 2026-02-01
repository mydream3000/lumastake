<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('transactions', 'is_real')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->boolean('is_real')->default(false)->after('amount');
                $table->index('is_real');
            });

            // Migrate data from meta->is_real to the new column
            $transactions = DB::table('transactions')->whereNotNull('meta')->get();
            foreach ($transactions as $tx) {
                $meta = json_decode($tx->meta, true);
                if (isset($meta['is_real']) && $meta['is_real']) {
                    DB::table('transactions')->where('id', $tx->id)->update(['is_real' => true]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('is_real');
        });
    }
};
