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
    Schema::table('sales', function (Blueprint $table) {
      $table->timestamp('sold_on')->after('sale_type');
      $table->string('sale_pdf_path')->nullable()->after('full_response');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('sales', function (Blueprint $table) {
      $table->dropColumn(['sold_on', 'sale_pdf_path']);
    });
  }
};
