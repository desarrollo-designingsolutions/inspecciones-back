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
            $table->foreignUuid('client_id')->constrained();
            $table->string('gross_vehicle_weight')->nullable();
            $table->string('passenger_capacity')->nullable();
            $table->string('number_axles')->nullable();
            $table->string('current_mileage')->nullable();
            $table->boolean('have_trailer')->default(false);
            $table->string('trailer')->nullable();
            $table->foreignUuid('vehicle_structure_id')->constrained();

            //Tab 2: Documentos del Vehículo
            // $table->foreignUuid('type_document_id')->nullable()->constrained();
            // $table->string('document_number')->nullable();
            // $table->date('date_issue')->nullable();
            // $table->date('expiration_date')->nullable();

            //Tab 3: Fotografías del Vehículo
            $table->string('photo_front')->nullable();
            $table->string('photo_rear')->nullable();
            $table->string('photo_right_side')->nullable();
            $table->string('photo_left_side')->nullable();




            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

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
