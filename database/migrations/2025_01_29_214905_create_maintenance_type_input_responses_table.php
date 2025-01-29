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
        Schema::create('maintenance_type_input_responses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('maintenance_id');
            $table->foreignId('maintenance_type_input_id');
            $table->foreignId('user_id');
            $table->string('type');
            $table->string('type_maintenance');
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_type_input_responses');
    }
};
