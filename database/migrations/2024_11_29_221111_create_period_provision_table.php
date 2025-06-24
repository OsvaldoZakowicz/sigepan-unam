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
    Schema::create('period_provision', function (Blueprint $table) {
      $table->id();

      // fk periodo de peticion de suministros
      // puedo borrar un periodo asociado a suministros
      $table->unsignedBigInteger('period_id');
      $table->foreign('period_id')
        ->references('id')
        ->on('request_for_quotation_periods')
        ->cascadeOnDelete();

      //fk suministros
      // no puedo borrar un suministro asociado a un periodo
      $table->unsignedBigInteger('provision_id');
      $table->foreign('provision_id')
        ->references('id')
        ->on('provisions')
        ->restrictOnDelete();

      // cantidad a presupuestar
      $table->smallInteger('quantity', false, true);

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('period_provision');
  }
};
