<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Sembrado en orden.
     */
    public function run(): void
    {
        $this->call(
            PermissionSeeder::class,
            RoleSeeder::class
        );
    }
}
