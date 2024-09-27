<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenderSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    DB::table('genders')->insert([
      ['gender' => 'femenino', 'created_at' => now(env('APP_TIMEZONE'))],
      ['gender' => 'masculino', 'created_at' => now(env('APP_TIMEZONE'))],
      ['gender' => 'otro', 'created_at' => now(env('APP_TIMEZONE'))],
    ]);
  }
}
