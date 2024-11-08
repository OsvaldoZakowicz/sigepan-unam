<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvisionTrademarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('provision_trademarks')->insert([
          ['provision_trademark_name' => 'otro/a',            'created_at' => now(env('APP_TIMEZONE'))], // default
          ['provision_trademark_name' => 'blancaflor',        'created_at' => now(env('APP_TIMEZONE'))],
          ['provision_trademark_name' => 'pureza',            'created_at' => now(env('APP_TIMEZONE'))],
          ['provision_trademark_name' => 'natura',            'created_at' => now(env('APP_TIMEZONE'))],
          ['provision_trademark_name' => 'calsa',             'created_at' => now(env('APP_TIMEZONE'))],
          ['provision_trademark_name' => 'dos anclas',        'created_at' => now(env('APP_TIMEZONE'))],
          ['provision_trademark_name' => 'marolio',           'created_at' => now(env('APP_TIMEZONE'))],
          ['provision_trademark_name' => 'ledesma',           'created_at' => now(env('APP_TIMEZONE'))],
          ['provision_trademark_name' => 'la muñeca',         'created_at' => now(env('APP_TIMEZONE'))],
          ['provision_trademark_name' => 'la serenísima',     'created_at' => now(env('APP_TIMEZONE'))],
          ['provision_trademark_name' => 'milkaut',           'created_at' => now(env('APP_TIMEZONE'))],
          ['provision_trademark_name' => 'la campagnola',     'created_at' => now(env('APP_TIMEZONE'))],
          ['provision_trademark_name' => 'arcor',             'created_at' => now(env('APP_TIMEZONE'))],
          ['provision_trademark_name' => 'sancor',            'created_at' => now(env('APP_TIMEZONE'))],
          ['provision_trademark_name' => 'bolsaplast',        'created_at' => now(env('APP_TIMEZONE'))],
          ['provision_trademark_name' => 'inapack',           'created_at' => now(env('APP_TIMEZONE'))],
          ['provision_trademark_name' => 'empacar',           'created_at' => now(env('APP_TIMEZONE'))],
          ['provision_trademark_name' => 'envases del plata', 'created_at' => now(env('APP_TIMEZONE'))],
        ]);
    }
}
