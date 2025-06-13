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
    Schema::create('purchases', function (Blueprint $table) {
      $table->id();
      // fk proveedor
      $table->foreignId('supplier_id')
        ->constrained('suppliers')->restrictOnDelete();
      $table->timestamp('purchase_date');
      $table->decimal('total_price', 10, 2);
      $table->unsignedBigInteger('purchase_reference_id')->nullable(); // preorden de compra referente
      $table->string('purchase_reference_type')->nullable(); // preorden de compra referente
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('purchases');
  }
};
