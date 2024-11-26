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
    Schema::create('period_statuses', function (Blueprint $table) {
      $table->id();
      $table->string('status_name', 25)->unique();
      $table->smallInteger('status_code', false, true)->unique();
      $table->string('status_short_description', 100)->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('period_statuses');
  }
};
