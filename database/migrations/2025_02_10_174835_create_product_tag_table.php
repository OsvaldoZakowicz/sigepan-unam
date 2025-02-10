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
    Schema::create('product_tag', function (Blueprint $table) {
      $table->id();

      // fk products
      $table->unsignedBigInteger('product_id');
      $table->foreign('product_id')->references('id')
        ->on('products')->cascadeOnDelete();

      // fk tags
      $table->unsignedBigInteger('tag_id');
      $table->foreign('tag_id')->references('id')
        ->on('tags')->restrictOnDelete();

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('product_tag');
  }
};
