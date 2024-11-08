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
        Schema::create('provision_trademarks', function (Blueprint $table) {
            $table->id();
            $table->string('provision_trademark_name', 50)->unique();
            $table->boolean('provision_trademark_is_editable')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provision_trademarks');
    }
};
