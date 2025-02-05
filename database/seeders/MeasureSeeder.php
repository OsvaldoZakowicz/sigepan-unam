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
        'measure_name'      => 'kilogramos',
        'measure_abrv'      => 'kg',
        'measure_base'      => 1.000,
        'measure_base_abrv' => 'g',
        'measure_short_description' => 'mil gramos',
        'created_at' => now()
      ],
      [
        'measure_name'      => 'litros',
        'measure_abrv'      => 'lts',
        'measure_base'      => 1.000,
        'measure_base_abrv' => 'mL',
        'measure_short_description' => 'mil mililitros',
        'created_at' => now()
      ],
      [
        'measure_name'      => 'unidad',
        'measure_abrv'      => 'un',
        'measure_base'      => 1.000,
        'measure_base_abrv' => 'un',
        'measure_short_description' => 'una unidad',
        'created_at' => now()
      ],
    ]);

  }
}
