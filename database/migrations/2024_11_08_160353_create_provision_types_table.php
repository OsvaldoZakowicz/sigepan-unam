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
        Schema::create('provision_types', function (Blueprint $table) {
            $table->id();
            $table->string('provision_type_name', 50)->unique();
            $table->string('provision_type_short_description', 150)->nullable();
            $table->boolean('provision_type_is_editable')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provision_types');
    }
};
