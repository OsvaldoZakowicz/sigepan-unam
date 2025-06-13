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
    Schema::create('existences', function (Blueprint $table) {
      $table->id();
      // fk provisions
      $table->foreignId('provision_id')
        ->constrained('provisions')->restrictOnDelete();
      // fk purchases
      $table->foreignId('purchase_id')
        ->nullable()
        ->constrained('purchases')->restrictOnDelete();
      // fk stocks
      $table->foreignId('stock_id')
        ->nullable()
        ->constrained('stocks')->restrictOnDelete();
      $table->enum('movement_type', ['compra', 'elaboracion', 'perdida']);
      $table->timestamp('registered_at');
      $table->decimal('quantity_amount', 10, 3);  // permite hasta 3 decimales
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('existences');
  }
};
