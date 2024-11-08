<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvisionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('provision_types')->insert([
          [
            'provision_type_name' => 'ingrediente',
            'provision_type_short_description' => 'un ingrediente es un componente esencial de la receta de un producto',
            'created_at' => now(env('APP_TIMEZONE'))
          ],
          [
            'provision_type_name' => 'insumo',
            'provision_type_short_description' => 'un insumo es un bien necesario para producir o envasar un producto',
            'created_at' => now(env('APP_TIMEZONE'))
          ],
        ]);
    }
}
