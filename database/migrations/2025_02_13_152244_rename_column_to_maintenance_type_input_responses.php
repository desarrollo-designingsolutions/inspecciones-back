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
        Schema::table('maintenance_type_input_responses', function (Blueprint $table) {
            $table->renameColumn('user_inspector_id', 'user_made_maintenance_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_type_input_responses', function (Blueprint $table) {
            $table->renameColumn('user_made_maintenance_id', 'user_inspector_id');
        });
    }
};
