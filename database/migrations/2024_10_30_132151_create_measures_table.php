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
        Schema::create('measures', function (Blueprint $table) {
            $table->id();
            $table->string('measure_name', 50)->unique();
            $table->string('measure_abrv', 4)->unique();
            $table->integer('measure_base', false, true);
            $table->string('measure_short_description', 150)->nullable();
            $table->boolean('measure_is_editable')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('measures');
    }
};
