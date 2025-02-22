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
    Schema::create('orders', function (Blueprint $table) {
      $table->id();
      $table->string('order_code')->unique();

      // fk order status
      // no puedo borrar un estado de orden si tiene ordenes asociadas
      $table->unsignedBigInteger('order_status_id');
      $table->foreign('order_status_id')
        ->references('id')->on('order_statuses')->restrictOnDelete();

      // fk user
      // no puedo borrar un usuario si tiene ordenes asociadas
      // un usuario puede ser null si la venta es a 'consumidor final'
      $table->unsignedBigInteger('user_id')->nullable();
      $table->foreign('user_id')
        ->references('id')->on('users')->restrictOnDelete();

      // fk user (empleado)
      // puede ser null en caso de pedidos online o automáticos
      $table->unsignedBigInteger('employee_id')->nullable();
      $table->foreign('employee_id')
          ->references('id')->on('users')->restrictOnDelete();

      $table->enum('order_origin', ['WEB', 'PRESENCIAL']);
      $table->decimal('total_price', 10, 2);
      $table->timestamp('delivered_at')->nullable();

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('orders');
  }
};
