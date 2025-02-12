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
    Schema::table('recipes', function (Blueprint $table) {
      // fk product_id
      // borrar un producto elimina sus recetas
      $table->unsignedBigInteger('product_id')
        ->after('recipe_instructions');
      $table->foreign('product_id')->references('id')
        ->on('products')->cascadeOnDelete();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('recipes', function (Blueprint $table) {
      $table->dropForeign(['product_id']);
      $table->dropColumn('product_id');
    });
  }
};
