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
    Schema::create('pre_order_pack', function (Blueprint $table) {
      $table->id();
      // fk pre_orders
      $table->unsignedBigInteger('pre_order_id');
      $table->foreign('pre_order_id')->references('id')->on('pre_orders')->restrictOnDelete();
      // fk packs
      $table->unsignedBigInteger('pack_id');
      $table->foreign('pack_id')->references('id')->on('packs')->restrictOnDelete();
      $table->boolean('has_stock');
      $table->smallInteger('quantity', false, true);
      $table->decimal('unit_price');
      $table->decimal('total_price');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('pre_order_pack');
  }
};
