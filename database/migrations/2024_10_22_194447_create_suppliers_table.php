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
    Schema::create('suppliers', function (Blueprint $table) {
      $table->id();
      $table->string('company_name', 90)->unique();
      $table->string('company_cuit', 11)->unique();
      $table->string('iva_condition', 50);
      $table->string('phone_number', 10)->unique();
      $table->string('short_description', 150)->nullable();
      $table->boolean('status_is_active')->default(true);
      $table->string('status_description', 150)->nullable();
      $table->date('status_date');

      // fk addresses
      //? que sucede con el proveedor si borro la direccion?
      // no puedo borrar una direccion mientras este asociada a un proveedor
      // si borro el proveedor, borrar manualmente la direccion
      $table->unsignedBigInteger('address_id');
      $table->foreign('address_id')->references('id')->on('addresses')
        ->restrictOnDelete();

      // fk users
      //? que sucede con el proveedor si borro el usuario?
      // no puedo borrar un usuario mientras este asociado a un proveedor
      // si borro el proveedor, borrar manualmente la cuenta de usuario
      $table->unsignedBigInteger('user_id');
      $table->foreign('user_id')->references('id')->on('users')
        ->restrictOnDelete();

      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('suppliers');
  }
};
