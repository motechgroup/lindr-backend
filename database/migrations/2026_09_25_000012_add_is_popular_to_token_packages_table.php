<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('token_packages')) {
            Schema::table('token_packages', function (Blueprint $table) {
                if (!Schema::hasColumn('token_packages', 'is_popular')) {
                    $table->boolean('is_popular')->default(false)->after('badge');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('token_packages')) {
            Schema::table('token_packages', function (Blueprint $table) {
                if (Schema::hasColumn('token_packages', 'is_popular')) {
                    $table->dropColumn('is_popular');
                }
            });
        }
    }
};
