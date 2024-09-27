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
    // seeders
    $this->call([
      PermissionSeeder::class,
      RoleSeeder::class
    ]);

    // usuario de pruebas
    $user = User::create([
      'name' => 'test',
      'email' => 'test@test.com',
      'password' => bcrypt(12345678)
    ]);

    $user->assignRole('administrador');
  }
}
