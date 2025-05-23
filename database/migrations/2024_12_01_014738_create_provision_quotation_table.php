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
    Schema::create('provision_quotation', function (Blueprint $table) {
      $table->id();

      // fk presupuesto (quotations)
      $table->unsignedBigInteger('quotation_id');
      $table->foreign('quotation_id')->references('id')->on('quotations')->restrictOnDelete();

      // fk suministros
      $table->unsignedBigInteger('provision_id');
      $table->foreign('provision_id')->references('id')->on('provisions')->restrictOnDelete();

      $table->boolean('has_stock')->default(false);
      $table->smallInteger('quantity', false, true);
      $table->decimal('unit_price')->nullable();
      $table->decimal('total_price')->nullable();

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('provision_quotation');
  }
};
