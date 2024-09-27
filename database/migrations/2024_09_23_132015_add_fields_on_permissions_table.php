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
    Schema::table('permissions', function (Blueprint $table) {
      $table->boolean('is_internal')
        ->after('guard_name')
        ->default(true);
      $table->string('short_description', 150)
        ->after('guard_name')
        ->default('descripcion corta')
        ->nullable(); // la descripcion puede ser nula
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('permissions', function (Blueprint $table) {
      $table->dropColumn(['short_description']);
    });
  }
};
