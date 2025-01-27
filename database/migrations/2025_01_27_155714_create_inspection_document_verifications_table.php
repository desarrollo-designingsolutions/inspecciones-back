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
            $table->id();
            $table->foreignUuid('inspection_id');
            $table->foreignUuid('vehicle_document_id');
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
