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
      $table->string('dni', 8)->unique();
      $table->date('birthdate');
      $table->string('phone_number', 10)->unique();

      // fk genders
      //? que sucede con el genero al borrar este perfil?
      // al borrar un perfil, el genero permanece
      // no se permite borrar un genero con perfiles asociados
      $table->unsignedBigInteger('gender_id');
      $table->foreign('gender_id')->references('id')->on('genders')
        ->restrictOnDelete();

      // fk addresses
      //? que sucede con la direccion al borrar este perfil?
      // al borrar un perfil, la direccion permanece (debe eliminarse manualmente)
      // no se pemite borrar una direccion con un perfil asociado
      $table->unsignedBigInteger('address_id');
      $table->foreign('address_id')->references('id')->on('addresses')
        ->restrictOnDelete();

      // fk users
      //? que sucede con el usuario al borrar este perfil
      // al borrar un perfil, el usuario permanece (debe eliminarse manualmente)
      // al borrar un usuario, el perfil es borrado
      $table->unsignedBigInteger('user_id');
      $table->foreign('user_id')->references('id')->on('users')
        ->cascadeOnDelete();

      $table->timestamps();
      $table->softDeletes();
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
