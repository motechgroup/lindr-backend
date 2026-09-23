<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('token_packages')) {
            Schema::create('token_packages', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->integer('tokens');
                $table->decimal('price_usd', 8, 2);
                $table->string('badge')->nullable();
                $table->boolean('is_popular')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('token_packages');
    }
};
