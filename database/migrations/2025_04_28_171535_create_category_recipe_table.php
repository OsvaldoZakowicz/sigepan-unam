<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   * tabla intermdia entre Recipes y Provision category
   */
  public function up(): void
  {
    Schema::create('category_recipe', function (Blueprint $table) {
      $table->id();
      $table->foreignId('recipe_id')
        ->constrained('recipes')->onDelete('cascade');
      $table->foreignId('category_id')
        ->constrained('provision_categories')->onDelete('cascade');
      $table->decimal('quantity', 8, 2);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('category_recipe');
  }
};
