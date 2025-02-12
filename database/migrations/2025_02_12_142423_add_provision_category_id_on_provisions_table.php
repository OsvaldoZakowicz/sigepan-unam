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
    Schema::table('provisions', function (Blueprint $table) {
      // fk provision_category_id
      $table->unsignedBigInteger('provision_category_id')
        ->after('provision_trademark_id');
      $table->foreign('provision_category_id')->references('id')
        ->on('provision_categories')->restrictOnDelete();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('provisions', function (Blueprint $table) {
      $table->dropForeign(['provision_category_id']);
      $table->dropColumn('provision_category_id');
    });
  }
};
