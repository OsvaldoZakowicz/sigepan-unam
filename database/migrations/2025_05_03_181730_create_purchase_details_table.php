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
    Schema::create('purchase_details', function (Blueprint $table) {
      $table->id();
      // fk purchases
      $table->foreignId('purchase_id')
        ->constrained('purchases')->restrictOnDelete();
      // fk provisions
      $table->foreignId('provision_id')
        ->nullable()
        ->constrained('provisions')->restrictOnDelete();
      // fk packs
      $table->foreignId('pack_id')
        ->nullable()
        ->constrained('packs')->restrictOnDelete();
      $table->smallInteger('item_count');
      $table->decimal('unit_price', 10, 2);
      $table->decimal('subtotal_price', 10, 2);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('purchase_details');
  }
};
