<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PeriodStatus;

class PeriodStatusSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    PeriodStatus::create([
      'status_name' => 'programado',
      'status_code' => 0,
      'status_short_description' => 'el periodo de solicitud esta programado para la fecha de inicio',
    ]);

    PeriodStatus::create([
      'status_name' => 'abierto',
      'status_code' => 1,
      'status_short_description' => 'el periodo de solicitud esta abierto',
    ]);

    PeriodStatus::create([
      'status_name' => 'cerrado',
      'status_code' => 2,
      'status_short_description' => 'el periodo de solicitud esta cerrado',
    ]);
  }
}
