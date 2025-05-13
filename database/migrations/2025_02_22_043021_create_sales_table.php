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
      // fk order_id (nullable)
      $table->foreignId('order_id')
        ->nullable()
        ->constrained()->restrictOnDelete();
      // fk user_id (nullable)
      $table->foreignId('user_id')
        ->nullable()
        ->constrained()->restrictOnDelete();
      $table->enum('client_type', ['cliente registrado', 'cliente no registrado']);
      $table->enum('sale_type', ['venta web', 'venta presencial']);
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
