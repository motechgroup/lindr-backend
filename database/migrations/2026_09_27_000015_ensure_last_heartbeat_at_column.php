<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'last_heartbeat_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('last_heartbeat_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'last_heartbeat_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('last_heartbeat_at');
            });
        }
    }
};
