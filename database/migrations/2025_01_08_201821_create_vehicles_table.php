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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();

            //Tab 1: Información General

            $table->foreignUuid('company_id')->constrained();
            $table->string('license_plate');
            $table->foreignUuid('type_vehicle_id')->constrained();
            $table->date('date_registration');
            $table->foreignUuid('brand_vehicle_id')->constrained();
            $table->string('engine_number');
            $table->foreignId('state_id')->constrained();
            $table->foreignId('city_id')->constrained();
            $table->string('model');
            $table->string('vin_number');
            $table->string('load_capacity');
            $table->foreignId('client_id')->constrained();
            $table->string('gross_vehicle_weight')->nullable();
            $table->string('passenger_capacity')->nullable();
            $table->string('number_axles')->nullable();
            $table->string('current_mileage')->nullable();
            $table->boolean('have a trailer')->default(false);
            $table->string('trailer')->nullable();
            $table->foreignId('vehicle_structure_id')->constrained();

            //Tab 2: Documentos del Vehículo
            $table->foreignId('type_document_id')->constrained();
            $table->string('document_number');
            $table->date('date_issue');
            $table->date('expiration_date');

            //Tab 3: Fotografías del Vehículo
            $table->string('front');
            $table->string('rear');
            $table->string('right_side');
            $table->string('left_side');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
