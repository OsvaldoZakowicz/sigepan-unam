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
    Schema::table('product_sale', function (Blueprint $table) {
      $table->string('details')
        ->nullable()->after('subtotal_price');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('product_sale', function (Blueprint $table) {
      $table->dropColumn(['details']);
    });
  }
};
