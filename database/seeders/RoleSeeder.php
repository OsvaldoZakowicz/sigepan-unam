<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
  /**
   * Run the database seeds.
   * Para roles, hare una insercion en la DB sin usar create()
   * del modelo Role, ya que se trata de un paquete de spatie.
   */
  public function run(): void
  {
    DB::table('roles')->insert([
      ['name' => 'gerente',       'guard_name' => 'web', 'is_editable' => false, 'is_internal' => true, 'created_at' => now(env('APP_TIMEZONE'))],
      ['name' => 'panadero',      'guard_name' => 'web', 'is_editable' => false, 'is_internal' => true, 'created_at' => now(env('APP_TIMEZONE'))],
      ['name' => 'vendedor',      'guard_name' => 'web', 'is_editable' => false, 'is_internal' => true, 'created_at' => now(env('APP_TIMEZONE'))],
      ['name' => 'repartidor',    'guard_name' => 'web', 'is_editable' => false, 'is_internal' => true, 'created_at' => now(env('APP_TIMEZONE'))],
      ['name' => 'proveedor',     'guard_name' => 'web', 'is_editable' => false, 'is_internal' => true, 'created_at' => now(env('APP_TIMEZONE'))],
      ['name' => 'administrador', 'guard_name' => 'web', 'is_editable' => false, 'is_internal' => true, 'created_at' => now(env('APP_TIMEZONE'))],
      ['name' => 'auditor',       'guard_name' => 'web', 'is_editable' => false, 'is_internal' => true, 'created_at' => now(env('APP_TIMEZONE'))],
      ['name' => 'cliente',       'guard_name' => 'web', 'is_editable' => false, 'is_internal' => false, 'created_at' => now(env('APP_TIMEZONE'))],
    ]);
  }
}
