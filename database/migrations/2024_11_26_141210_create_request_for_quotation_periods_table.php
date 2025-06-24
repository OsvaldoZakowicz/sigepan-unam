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
    Schema::create('request_for_quotation_periods', function (Blueprint $table) {
      $table->id();
      $table->string('period_code', 25)->unique();
      $table->date('period_start_at');
      $table->date('period_end_at');
      $table->string('period_short_description', 150)->nullable();

      // fk a estados del periodo, period_statuses
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
    Schema::dropIfExists('request_for_quotation_periods');
  }
};
