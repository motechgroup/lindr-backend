<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add missing amount_credits column to transactions table if not present.
     */
    public function up(): void
    {
        if (Schema::hasTable('transactions')) {
            if (!Schema::hasColumn('transactions', 'amount_credits')) {
                Schema::table('transactions', function (Blueprint $table) {
                    $table->integer('amount_credits')->default(0)->after('amount_tokens');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('transactions') && Schema::hasColumn('transactions', 'amount_credits')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropColumn('amount_credits');
            });
        }
    }
};
