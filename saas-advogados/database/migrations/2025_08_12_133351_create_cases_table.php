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
        Schema::create('cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId("tenant_id")->constrained("tenants")->onDelete("cascade");
            $table->foreignId("client_id")->constrained("clients")->onDelete("cascade");
            $table->foreignId("lawyer_id")->nullable()->constrained("lawyers")->onDelete("set null");
            $table->string("case_number")->nullable();
            $table->string("case_type");
            $table->string("court")->nullable();
            $table->string("status")->default("pending");
            $table->text("description")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cases');
    }
};
