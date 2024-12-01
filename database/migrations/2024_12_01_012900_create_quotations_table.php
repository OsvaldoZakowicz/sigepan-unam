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
    Schema::create('quotations', function (Blueprint $table) {
      $table->id();
      $table->string('quotation_id', 25)->unique();

      // fk periodo de peticion de presupuestos
      $table->unsignedBigInteger('period_id');
      $table->foreign('period_id')->references('id')->on('request_for_quotation_periods')->restrictOnDelete();

      // fk proveedor
      $table->unsignedBigInteger('supplier_id');
      $table->foreign('supplier_id')->references('id')->on('suppliers')->restrictOnDelete();

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('quotations');
  }
};
