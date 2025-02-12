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
  public function run(): void
  {

    DB::table('measures')->insert([
      [
        'unit_name' => 'kilogramo',
        'base_value' => 1,
        'unit_symbol' => 'kg',
        'conversion_unit' => 'gramos',
        'conversion_factor' => 1000,
        'conversion_symbol' => 'g',
        'short_description' => 'unidad de medida en kilogramos'
      ],
      [
        'unit_name' => 'metro',
        'base_value' => 1,
        'unit_symbol' => 'm',
        'conversion_unit' => 'centimetros',
        'conversion_factor' => 100,
        'conversion_symbol' => 'cm',
        'short_description' => 'unidad de medida en metros'
      ],
      [
        'unit_name' => 'litro',
        'base_value' => 1,
        'unit_symbol' => 'L',
        'conversion_unit' => 'mililitros',
        'conversion_factor' => 1000,
        'conversion_symbol' => 'mL',
        'short_description' => 'unidad de medida en litros'
      ],
      [
        'unit_name' => 'unidad',
        'base_value' => 1,
        'unit_symbol' => 'u',
        'conversion_unit' => null,
        'conversion_factor' => null,
        'conversion_symbol' => null,
        'short_description' => 'unidad de medida para cantidad unitaria'
      ]
    ]);
  }
}
