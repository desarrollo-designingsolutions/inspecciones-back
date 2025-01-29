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
            $table->string('document')->nullable();
            $table->foreignUuid('type_license_id')->nullable()->constrained('type_licenses');
            $table->string('license')->nullable();
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
            $table->dropColumn('document');
            $table->dropConstrainedForeignId('type_license_id');
            $table->dropColumn('license');
            $table->dropColumn('expiration_date');
        });
    }
};
