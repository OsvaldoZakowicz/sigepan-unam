<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IvaConditionSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    DB::table('iva_conditions')->insert([
      [
        'name' => 'iva responsable inscripto',
        'description' => 'iva Responsable Inscripto',
        'created_at' => now(env('APP_TIMEZONE')),
        'updated_at' => now(env('APP_TIMEZONE'))
      ],
      [
        'name' => 'monotributista',
        'description' => 'monotributista',
        'created_at' => now(env('APP_TIMEZONE')),
        'updated_at' => now(env('APP_TIMEZONE'))
      ],
      [
        'name' => 'iva exento',
        'description' => 'iva exento',
        'created_at' => now(env('APP_TIMEZONE')),
        'updated_at' => now(env('APP_TIMEZONE'))
        ]
    ]);
  }
}
