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
        Schema::table('tenants', function (Blueprint $table) {
            // Adiciona apenas campos que não existem
            if (!Schema::hasColumn('tenants', 'last_payment_at')) {
                $table->timestamp('last_payment_at')->nullable()->after('trial_ends_at');
            }
            if (!Schema::hasColumn('tenants', 'next_billing_date')) {
                $table->timestamp('next_billing_date')->nullable()->after('last_payment_at');
            }
            if (!Schema::hasColumn('tenants', 'monthly_amount')) {
                $table->decimal('monthly_amount', 10, 2)->nullable()->after('next_billing_date');
            }
            if (!Schema::hasColumn('tenants', 'payment_methods')) {
                $table->json('payment_methods')->nullable()->after('monthly_amount');
            }
            if (!Schema::hasColumn('tenants', 'customer_id')) {
                $table->string('customer_id')->nullable()->after('payment_methods');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'last_payment_at',
                'next_billing_date',
                'monthly_amount',
                'payment_methods',
                'customer_id'
            ]);
        });
    }
};
