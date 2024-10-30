<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MeasureSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void {

    DB::table('measures')->insert([
      [
        'measure_name' => 'gramos',
        'measure_abrv' => 'gr',
        'measure_base' => 1000,
        'measure_short_description' => 'unidad de medida en gramos',
        'measure_is_editable' => false,
        'created_at' => now()
      ],
      [
        'measure_name' => 'centimetros cubicos',
        'measure_abrv' => 'cc',
        'measure_base' => 1000,
        'measure_short_description' => 'unidad de medida en centimetros cubicos',
        'measure_is_editable' => false,
        'created_at' => now()
      ],
      [
        'measure_name' => 'unidad',
        'measure_abrv' => 'un',
        'measure_base' => 1,
        'measure_short_description' => 'unidad de medida en unidad',
        'measure_is_editable' => false,
        'created_at' => now()
      ],
      [
        'measure_name' => 'docena',
        'measure_abrv' => 'docn',
        'measure_base' => 12,
        'measure_short_description' => 'unidad de medida en docena',
        'measure_is_editable' => false,
        'created_at' => now()
      ],
    ]);

  }
}
