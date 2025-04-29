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
    Schema::create('stock_movements', function (Blueprint $table) {
      $table->id();
      // fk stocks
      $table->foreignId('stock_id')->constrained('stocks')->restrictOnDelete();
      $table->unsignedSmallInteger('quantity');
      $table->enum('movement_type', ['venta', 'elaboracion', 'perdida']);
      $table->date('registered_at');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('stock_movements');
  }
};
