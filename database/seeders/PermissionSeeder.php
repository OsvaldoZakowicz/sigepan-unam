<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Para permisos, hare una insercion en la DB sin usar create()
     * del modelo Permission, ya que se trata de un paquete de spatie.
     */
    public function run(): void
    {
        // permisos de modulos y secciones
        DB::table('permissions')->insert([
            ['name' => 'dashboard',     'guard_name' => 'web', 'created_at' => now(env('APP_TIMEZONE'))],
            ['name' => 'ecommerce',     'guard_name' => 'web', 'created_at' => now(env('APP_TIMEZONE'))],
            ['name' => 'usuarios',      'guard_name' => 'web', 'created_at' => now(env('APP_TIMEZONE'))],
            ['name' => 'stock',         'guard_name' => 'web', 'created_at' => now(env('APP_TIMEZONE'))],
            ['name' => 'proveedores',   'guard_name' => 'web', 'created_at' => now(env('APP_TIMEZONE'))],
            ['name' => 'ventas',        'guard_name' => 'web', 'created_at' => now(env('APP_TIMEZONE'))],
            ['name' => 'compras',       'guard_name' => 'web', 'created_at' => now(env('APP_TIMEZONE'))],
        ]);
    }
}
