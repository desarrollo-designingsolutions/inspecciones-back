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
        Schema::table('maintenances', callback: function (Blueprint $table) {
            $table->foreignUuid('user_made_maintenance_id')->after('user_mechanic_id')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            // 1. Eliminar la restricción de clave foránea
            $table->dropForeign(['user_made_maintenance_id']);

            // 2. Eliminar la columna
            $table->dropColumn('user_made_maintenance_id');
        });
    }
};
