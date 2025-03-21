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
        Schema::create('inspection_document_verifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('inspection_id')->constrained();
            $table->foreignUuid('vehicle_document_id')->constrained();
            $table->boolean('original')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_document_verifications');
    }
};
