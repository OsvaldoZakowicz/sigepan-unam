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
      // * permisos y roles
      PermissionSeeder::class,
      RoleSeeder::class,

      // * generos
      GenderSeeder::class,

      // * usuarios (siempre despues de roles y permisos)
      // usuarios internos de prueba
      UserSeeder::class,
      // clientes de prueba para la tienda
      ClientSeeder::class,

      // * suministros
      // unidades de medida
      MeasureSeeder::class,
      // tipos de suministros
      ProvisionTypeSeeder::class,
      // marcas de suministros
      ProvisionTrademarkSeeder::class,
      // categorias de suministros (siempre despues de unidad de medida y tipo)
      ProvisionCategorySeeder::class,
      // suministros (siempre antes de proveedor y precios)
      ProvisionSeeder::class,

      // * proveedores
      // condiciones frente al iva
      IvaConditionSeeder::class,
      SupplierSeeder::class,
      PricesSeeder::class,

      // * periodos de presupustos y ordenes de compra
      PeriodStatusSeeder::class,

      // * compras y existencias
      PurchaseSeeder::class,

      // * productos
      // etiquetas de productos
      TagSeeder::class,
      // todo: seeder de productos con precio
      // todo: seeder de recetas
      // todo: seeder de elaboracion de productos

      // * ventas
      // estado de las ordenes
      OrderStatusSeeder::class,
      // todo: seeder de ventas con movimiento de stock

    ]);

  }
}
