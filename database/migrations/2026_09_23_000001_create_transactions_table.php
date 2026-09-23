<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('type'); // topup, gift, call_deduction, credit_payout
                $table->integer('amount_tokens')->default(0);
                $table->integer('amount_credits')->default(0);
                $table->decimal('amount_usd', 8, 2)->default(0.00);
                $table->string('payment_provider')->nullable(); // flutterwave, mpesa
                $table->string('reference')->nullable()->index();
                $table->string('status')->default('completed'); // pending, completed, failed
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
