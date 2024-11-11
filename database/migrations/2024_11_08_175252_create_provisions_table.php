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
        Schema::create('provisions', function (Blueprint $table) {
            $table->id();
            $table->string('provision_name', 50);
            $table->decimal('provision_quantity',6 ,2); // numeros de 0.00 a 999.99
            $table->string('provision_short_description', 50)->nullable();

            // * fk provision trademarks
            // no puedo borrar una marca relacionada a suministros
            $table->unsignedBigInteger('provision_trademark_id');
            $table->foreign('provision_trademark_id')
              ->references('id')->on('provision_trademarks')->restrictOnDelete();

            // * fk provision types
            // no puedo borrar un tipo de suministro relacionado a suministros
            $table->unsignedBigInteger('provision_type_id');
            $table->foreign('provision_type_id')
              ->references('id')->on('provision_types')->restrictOnDelete();

            // * fk measures
            // no puedo borrar una unidad de medida relacionada a suministros
            $table->unsignedBigInteger('measure_id');
            $table->foreign('measure_id')
              ->references('id')->on('measures')->restrictOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provisions');
    }
};
