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
    Schema::create('sales', function (Blueprint $table) {
      $table->id();
      // fk order_id
      // no puedo borrar una orden si tiene una venta asociada
      $table->foreignId('order_id')->constrained()->restrictOnDelete();
      $table->string('payment_type');
      $table->string('payment_id')->nullable();
      $table->string('status')->nullable();
      $table->string('external_reference')->nullable();
      $table->string('merchant_order_id')->nullable();
      $table->decimal('total_price');
      $table->json('full_response')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('sales');
  }
};
