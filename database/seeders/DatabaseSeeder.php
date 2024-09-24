<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   * Sembrado en orden.
   */
  public function run(): void
  {
    // usuario de pruebas, sin permisos ni roles
    User::create([
      'name' => 'test',
      'email' => 'test@test.com',
      'password' => bcrypt(12345678)
    ]);

    // seeders
    $this->call([
      PermissionSeeder::class,
      RoleSeeder::class
    ]);
  }
}
