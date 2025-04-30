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
      $table->foreignId('stock_id')
        ->constrained('stocks')->restrictOnDelete();
      $table->integer('quantity'); // puede ser negativo para salidas
      $table->enum('movement_type', ['venta', 'elaboracion', 'merma', 'perdida']);
      $table->timestamp('registered_at');
      $table->unsignedBigInteger('movement_reference_id')->nullable(); // id de referencia al movimiento
      $table->string('movement_reference_type')->nullable(); // modelo de referencia al movimiento
      $table->timestamps();

      // indices para mejorar rendimiento en consultas
      $table->index('movement_type');
      $table->index('registered_at');
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
