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
    Schema::create('provision_recipe', function (Blueprint $table) {
      $table->id();

      // fk provisions
      // no puedo borrar un suministro que se usa en recetas
      $table->unsignedBigInteger('provision_id');
      $table->foreign('provision_id')->references('id')
        ->on('provisions')->restrictOnDelete();

      // fk recipes
      $table->unsignedBigInteger('recipe_id');
      $table->foreign('recipe_id')->references('id')
        ->on('recipes')->cascadeOnDelete();

      $table->decimal('recipe_quantity', 6, 2);

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('provision_recipe');
  }
};
