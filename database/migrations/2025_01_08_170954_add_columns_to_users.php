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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignUuid('type_document_id')->nullable()->constrained('user_type_documents');
            $table->string('type_document_name')->nullable();
            $table->foreignUuid('type_license_id')->nullable()->constrained('type_licenses');
            $table->string('type_license_name')->nullable();
            $table->date('expiration_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('type_document_id');
            $table->dropColumn('type_document_name');
            $table->dropConstrainedForeignId('type_license_id');
            $table->dropColumn('type_license_name');
            $table->dropColumn('expiration_date');
        });
    }
};
