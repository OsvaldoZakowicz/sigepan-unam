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
    Schema::create('datos_negocio', function (Blueprint $table) {
      $table->id();
      $table->string('clave', 50)->unique();
      $table->text('valor');
      $table->string('descripcion')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('datos_negocio');
  }
};
