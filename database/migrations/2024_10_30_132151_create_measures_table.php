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
    Schema::create('measures', function (Blueprint $table) {
      $table->id();
      $table->string('unit_name');  // kilogramo, metro, litro
      $table->decimal('base_value', 10, 2)->default(1);
      $table->string('unit_symbol');  // kg, m, l
      $table->string('conversion_unit')->nullable();  // gramos, centimetros, mililitros
      $table->decimal('conversion_factor', 10, 2)->nullable();
      $table->string('conversion_symbol')->nullable();  // g, cm, ml
      $table->string('short_description', 100)->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('measures');
  }
};
