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
    Schema::create('addresses', function (Blueprint $table) {
      $table->id();
      $table->string('street', 45);
      $table->string('number', 8)->nullable();
      $table->string('postal_code', 4);
      $table->string('city', 45);

      // una direccion pertenece a un usuario
      $table->unsignedBigInteger('profile_id');
      $table->foreign('profile_id')->references('id')
        ->on('profiles')->onDelete('cascade');

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('addresses');
  }
};
