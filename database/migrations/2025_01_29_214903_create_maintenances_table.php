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
        Schema::create('maintenances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained();
            $table->foreignUuid('vehicle_id')->constrained();
            $table->foreignUuid('maintenance_type_id')->constrained();
            $table->foreignUuid('user_mechanic_id')->constrained('users');
            $table->foreignUuid('user_operator_id')->constrained('users');
            $table->foreignUuid('user_inspector_id')->constrained('users');
            $table->foreignId('state_id')->nullable()->constrained();
            $table->foreignId('city_id')->nullable()->constrained();
            $table->date('maintenance_date');
            $table->integer('mileage');
            $table->text('general_comment')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
