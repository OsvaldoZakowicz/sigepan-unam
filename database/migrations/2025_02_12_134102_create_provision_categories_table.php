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
    Schema::create('provision_categories', function (Blueprint $table) {
      $table->id();
      $table->string('provision_category_name', 50)->unique();
      $table->boolean('provision_category_is_editable')->default(false);

      // fk unidades de medida
      // no puedo eliminar una unidad de medida relacionada a una categoria
      $table->unsignedBigInteger('measure_id');
      $table->foreign('measure_id')->references('id')
        ->on('measures')->restrictOnDelete();

      // fk tipo de suministro
      // no puedo eliminar un tipo de suministro relacionado a una categoria
      $table->unsignedBigInteger('provision_type_id');
      $table->foreign('provision_type_id')->references('id')
        ->on('provision_types')->restrictOnDelete();

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('provision_categories');
  }
};
