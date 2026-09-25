<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ensure channel_name and status columns exist in call_sessions table.
     */
    public function up(): void
    {
        if (Schema::hasTable('call_sessions')) {
            if (!Schema::hasColumn('call_sessions', 'channel_name')) {
                Schema::table('call_sessions', function (Blueprint $table) {
                    $table->string('channel_name')->nullable()->after('receiver_id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
