<?php

namespace Database\Seeders;

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
    // seeders
    $this->call([
      GenderSeeder::class,
      PermissionSeeder::class,
      RoleSeeder::class,
      // usuarios siempre despues de roles y permisos
      UserSeeder::class,
      MeasureSeeder::class,
      ProvisionTypeSeeder::class,
      // categorias de suministros siempr despues de unidad de medida y tipo
      ProvisionCategorySeeder::class,
      ProvisionTrademarkSeeder::class,
      PeriodStatusSeeder::class,
      IvaConditionSeeder::class,
      TagSeeder::class,
      OrderStatusSeeder::class,
    ]);

  }
}
