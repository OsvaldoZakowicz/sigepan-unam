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
    Schema::create('order_product', function (Blueprint $table) {
      $table->id();
      // fk order_id
      $table->foreignId('order_id')
        ->constrained()->onDelete('restrict');
      // fk product_id
      $table->foreignId('product_id')
        ->constrained()->onDelete('restrict');
      $table->unsignedSmallInteger('order_quantity');
      $table->decimal('unit_price');
      $table->decimal('subtotal_price');
      $table->string('details');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('order_product');
  }
};
