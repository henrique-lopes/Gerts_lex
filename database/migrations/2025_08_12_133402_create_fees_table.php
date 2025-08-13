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
        Schema::create('fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId("case_id")->constrained("cases")->onDelete("cascade");
            $table->decimal("amount", 10, 2);
            $table->string("type"); // fixed/percentage
            $table->string("status")->default("pending");
            $table->date("due_date")->nullable();
            $table->date("paid_date")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};
