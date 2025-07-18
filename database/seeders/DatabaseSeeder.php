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
      // productos con precio y etiquetas (siempre despues de etiquetas)
      ProductSeeder::class,
      // recetas (siempre despues de productos, existencias, compras, categorias ...)
      RecipeSeeder::class,

      // * elaboracion de productos y ventas
      // estado de las ordenes (pedidos) del cliente
      OrderStatusSeeder::class,
      // elaboracion de productos + ventas
      ElaborationAndSalesSeeder::class,

      // * datos del negocio y tienda
      DatoNegocioSeeder::class,
      DatoTiendaSeeder::class,

    ]);

  }
}
