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
    Schema::create('recipes', function (Blueprint $table) {
      $table->id();
      $table->string('recipe_title', 50)->unique();
      $table->integer('recipe_yields', false, true)->default(1);
      $table->integer('recipe_portions', false, true)->default(1);
      $table->time('recipe_preparation_time');
      $table->string('recipe_instructions', 250);
      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('recipes');
  }
};
