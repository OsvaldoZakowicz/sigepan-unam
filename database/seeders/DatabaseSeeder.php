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
      GenderSeeder::class,
      PermissionSeeder::class,
      RoleSeeder::class,
      MeasureSeeder::class,
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
