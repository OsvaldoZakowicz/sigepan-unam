<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
  use WithoutModelEvents;

  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // administrador
    User::create([
      'name' => 'osvaldo',
      'email' => 'admin@test.com',
      'email_verified_at' => now(env('APP_TIMEZONE')),
      'password' => bcrypt(12345678),
      'is_first_login' => false,
    ])->assignRole('administrador');

    // auditor
    User::create([
      'name' => 'martin',
      'email' => 'audit@test.com',
      'email_verified_at' => now(env('APP_TIMEZONE')),
      'password' => bcrypt(12345678),
      'is_first_login' => false,
    ])->assignRole('auditor');

    // gerente
    User::create([
      'name' => 'mariano',
      'email' => 'gerente@test.com',
      'email_verified_at' => now(env('APP_TIMEZONE')),
      'password' => bcrypt(12345678),
      'is_first_login' => false,
    ])->assignRole('gerente');

    // panadero
    User::create([
      'name' => 'claudia',
      'email' => 'panadero@test.com',
      'email_verified_at' => now(env('APP_TIMEZONE')),
      'password' => bcrypt(12345678),
      'is_first_login' => false,
    ])->assignRole('panadero');

    // vendedor
    User::create([
      'name' => 'matias',
      'email' => 'vendedor@test.com',
      'email_verified_at' => now(env('APP_TIMEZONE')),
      'password' => bcrypt(12345678),
      'is_first_login' => false,
    ])->assignRole('vendedor');

  }
}
