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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId("tenant_id")->constrained("tenants")->onDelete("cascade");
            $table->foreignId("lawyer_id")->nullable()->constrained("lawyers")->onDelete("set null");
            $table->foreignId("case_id")->nullable()->constrained("cases")->onDelete("set null");
            $table->string("title");
            $table->text("description")->nullable();
            $table->dateTime("start_time");
            $table->dateTime("end_time");
            $table->string("type");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
