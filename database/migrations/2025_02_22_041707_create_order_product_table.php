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
      // no puedo borrar una orden si tiene productos
      $table->foreignId('order_id')
        ->constrained()->onDelete('restrict');

      // fk product_id
      // no puedo borrar un producto si tiene ordenes
      $table->foreignId('product_id')
        ->constrained()->onDelete('restrict');

      $table->unsignedSmallInteger('quantity');
      $table->decimal('unit_price', 10, 2);
      $table->decimal('subtotal_price', 10, 2);
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
