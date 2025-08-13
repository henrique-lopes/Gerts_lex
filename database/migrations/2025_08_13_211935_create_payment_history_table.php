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
        Schema::create('payment_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('payment_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->enum('plan', ['basic', 'premium']);
            $table->enum('method', ['credit_card', 'debit_card', 'pix', 'bank_transfer']);
            $table->enum('status', ['pending', 'approved', 'failed', 'canceled', 'refunded']);
            $table->json('payment_data')->nullable(); // Dados específicos do gateway
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->string('gateway_transaction_id')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['payment_id']);
            $table->index(['processed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_history');
    }
};
