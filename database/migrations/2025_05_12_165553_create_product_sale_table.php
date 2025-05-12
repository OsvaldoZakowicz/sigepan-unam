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
    Schema::create('product_sale', function (Blueprint $table) {
      $table->id();
      // fk a tabla sales
      $table->foreignId('sale_id')
        ->constrained('sales')->restrictOnDelete();
      // fk a tabla products
      $table->foreignId('product_id')
        ->constrained('products')->restrictOnDelete();
      $table->unsignedSmallInteger('sale_quantity');
      $table->decimal('unit_price');
      $table->decimal('subtotal_price');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('product_sale');
  }
};
