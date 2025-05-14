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
    Schema::create('prices', function (Blueprint $table) {
      $table->id();
      $table->foreignId('product_id')
        ->constrained()->onDelete('cascade');
      $table->smallInteger('quantity')->unsigned();
      $table->decimal('price', 10, 2);
      $table->string('description', 50);
      $table->boolean('is_default')->default(false);
      $table->timestamps();

      // Garantiza que no haya duplicados de cantidad para un mismo producto
      $table->unique(['product_id', 'quantity']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('prices');
  }
};
