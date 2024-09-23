<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * agrega tres columnas a la tabla roles
     */
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->string('short_description', 150)
                ->after('guard_name')
                ->default('descripcion corta')
                ->nullable(); // la descripcion puede ser nula
            $table->boolean('is_internal')
                ->after('guard_name')
                ->default(false);
            $table->boolean('is_editable')
                ->after('guard_name')
                ->default(true);
        });
    }

    /**
     * Reverse the migrations.
     * remueve las tres columnas de la tabla roles
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['short_description', 'is_internal', 'is_editable']);
        });
    }
};
