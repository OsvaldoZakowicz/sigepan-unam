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
    Schema::create('pack_supplier', function (Blueprint $table) {
      $table->id();

      // * fk suppliers
      // no puedo borrar un proveedor relacionado a un pack
      $table->unsignedBigInteger('supplier_id');
      $table->foreign('supplier_id')->references('id')->on('suppliers')->restrictOnDelete();

      // * fk packs
      // no puedo borrar un suministro relacionado a un pack
      $table->unsignedBigInteger('pack_id');
      $table->foreign('pack_id')->references('id')->on('packs')->restrictOnDelete();

      $table->decimal('price', 6, 2);

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('pack_supplier');
  }
};
