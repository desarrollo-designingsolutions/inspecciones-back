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
        Schema::create('inspection_input_responses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('inspection_id');
            $table->foreignUuid('inspection_type_input_id');
            $table->foreignUuid('user_id');
            $table->string('response');
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
        Schema::dropIfExists('inspection_input_responses');
    }
};
