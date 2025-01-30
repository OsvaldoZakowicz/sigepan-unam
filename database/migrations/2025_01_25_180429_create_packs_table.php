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
        Schema::create('packs', function (Blueprint $table) {
            $table->id();
            $table->string('pack_name', 45)->unique();
            $table->unsignedSmallInteger('pack_units');
            $table->decimal('pack_quantity', 6, 2);

            // * fk provisions
            // no pudo borrar un suministro relacionado a un pack
            $table->unsignedBigInteger('provision_id');
            $table->foreign('provision_id')->references('id')->on('provisions')->restrictOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packs');
    }
};
