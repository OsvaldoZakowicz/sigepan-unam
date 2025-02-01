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
    Schema::create('pack_period', function (Blueprint $table) {
      $table->id();

      // fk periodo de peticion de suministros
      // no puedo borrar un periodo asociado a packs
      $table->unsignedBigInteger('period_id');
      $table->foreign('period_id')->references('id')->on('request_for_quotation_periods')->restrictOnDelete();

      //fk packs
      // no puedo borrar un pack asociado a un periodo
      $table->unsignedBigInteger('pack_id');
      $table->foreign('pack_id')->references('id')->on('packs')->restrictOnDelete();

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
    Schema::dropIfExists('pack_period');
  }
};
