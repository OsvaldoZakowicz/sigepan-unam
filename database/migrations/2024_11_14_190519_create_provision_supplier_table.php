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
        Schema::create('provision_supplier', function (Blueprint $table) {
            $table->id();

            //* fk proveedores
            //no puedo borrar un proveedor asociado a un suministro
            $table->unsignedBigInteger('supplier_id');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->restrictOnDelete();

            //* fk suministros
            //no puedo borrar un suministro asociado a un proveedor
            $table->unsignedBigInteger('provision_id');
            $table->foreign('provision_id')->references('id')->on('provisions')->restrictOnDelete();

            // precio
            $table->decimal('price');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provision_supplier');
    }
};
