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
      $table->unsignedBigInteger('order_status_id');
      $table->foreign('order_status_id')
        ->references('id')->on('order_statuses')->restrictOnDelete();
      // fk user
      $table->unsignedBigInteger('user_id')->nullable();
      $table->foreign('user_id')
        ->references('id')->on('users')->restrictOnDelete();
      $table->decimal('total_price');
      $table->timestamp('ordered_at');
      $table->timestamp('delivered_at')->nullable();
      $table->string('payment_status');
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
