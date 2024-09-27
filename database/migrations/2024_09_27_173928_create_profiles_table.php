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
    Schema::create('profiles', function (Blueprint $table) {
      $table->id();
      $table->string('first_name', 50);
      $table->string('last_name', 50);
      $table->string('dni', 8);
      $table->date('birthdate');
      $table->string('phone_number', 10);

      // un perfil tiene un genero
      $table->unsignedBigInteger('gender_id');
      $table->foreign('gender_id')->references('id')->on('genders');

      // un perfil es de un usuario
      // borrar el usuario borra el perfil
      $table->unsignedBigInteger('user_id');
      $table->foreign('user_id')->references('id')
        ->on('users')->onDelete('cascade');

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('profiles');
  }
};
