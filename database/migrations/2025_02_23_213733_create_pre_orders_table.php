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
    Schema::create('pre_orders', function (Blueprint $table) {
      $table->id();
      // fk pre_order_periods
      $table->foreignId('pre_order_period_id')->constrained('pre_order_periods')->restrictOnDelete();
      $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
      $table->string('pre_order_code', 25)->unique();
      $table->string('quotation_reference', 25)->nullable();
      $table->enum('status', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
      $table->boolean('is_completed')->default(false);
      $table->boolean('is_approved_by_supplier')->default(false);
      $table->boolean('is_approved_by_buyer')->default(false);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('pre_orders');
  }
};
