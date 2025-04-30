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
    Schema::create('stocks', function (Blueprint $table) {
      $table->id();
      // fk products
      $table->foreignId('product_id')
        ->constrained('products')->restrictOnDelete();
      // fk recipes
      $table->foreignId('recipe_id')
        ->constrained('recipes')->restrictOnDelete();
      $table->string('lote_code', 30)->unique();
      $table->unsignedSmallInteger('quantity_total');
      $table->unsignedSmallInteger('quantity_left');
      $table->timestamp('elaborated_at');
      $table->timestamp('expired_at');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('stocks');
  }
};
