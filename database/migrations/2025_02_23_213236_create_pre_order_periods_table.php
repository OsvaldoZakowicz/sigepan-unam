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
    Schema::create('pre_order_periods', function (Blueprint $table) {
      $table->id();
      $table->string('period_code', 25)->unique();
      $table->date('period_start_at');
      $table->date('period_end_at');
      $table->string('period_short_description', 150)->nullable();
      // datos de pre ordenes en formato json
      $table->json('period_preorders_data')->nullable();

      // fk request for quotation periods, puede ser null
      $table->unsignedBigInteger('quotation_period_id')->nullable();
      $table->foreign('quotation_period_id')->references('id')->on('request_for_quotation_periods');

      // fk a estados del periodo, period_statuses
      // no puedo borrar periodos asociados a periodos de solicitud
      $table->unsignedBigInteger('period_status_id');
      $table->foreign('period_status_id')->references('id')->on('period_statuses')->restrictOnDelete();

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('pre_order_periods');
  }
};
