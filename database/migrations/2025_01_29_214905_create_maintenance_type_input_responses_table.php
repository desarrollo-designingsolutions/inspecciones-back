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
            $table->foreignUuid('maintenance_id');
            $table->foreignUuid('maintenance_type_input_id');
            $table->foreignUuid('user_id')->nullable();
            $table->string('type')->nullable();
            $table->string('type_maintenance')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
