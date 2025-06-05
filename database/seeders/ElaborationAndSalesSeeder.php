<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Services\Stock\StockService;
use App\Models\Sale;
use App\Services\Sale\SaleService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ElaborationAndSalesSeeder extends Seeder
{
  use WithoutModelEvents;

  protected $stock_service;
  protected $sale_service;

  public function __construct()
  {
    $this->stock_service = new StockService();
    $this->sale_service = new SaleService();
  }

  /**
   * run the database seeds.
   */
  public function run(): void
  {
    // obtener productos que tienen recetas
    $products = Product::has('recipes')->get();

    // iniciar desde hace 7 dias
    $start_date = Carbon::now()->subDays(7);

    // simular una semana de elaboraciones
    for ($day = 0; $day < 7; $day++) {

      // establecer la fecha base para este dia
      $current_date = $start_date->copy()->addDays($day);

      // realizar elaboraciones del dia
      $this->elaborateProducts($products, $current_date);

      // realizar ventas del dia
      $this->createDailySales($products, $current_date);
    }
  }

  /**
   * realizar elaboraciones de productos para un dia
   */
  private function elaborateProducts($products, $current_date): void
  {
    foreach ($products as $product) {
      // obtener una receta aleatoria del producto
      $recipe = $product->recipes()->inRandomOrder()->first();

      if (!$recipe) {
        continue;
      }

      // numero aleatorio de elaboraciones (entre 2 y 4)
      $elaborations = rand(2, 4);

      // realizar las elaboraciones
      for ($i = 0; $i < $elaborations; $i++) {
        try {
          // preparar los datos del stock
          $stock_data = [
            'product_id'    => $product->id,
            'recipe_id'     => $recipe->id,
            'elaborated_at' => $current_date,
            'expired_at'    => $current_date->copy()->addDays($product->product_expires_in),
          ];

          // crear el stock y su movimiento inicial usando el servicio
          $this->stock_service->createStock($stock_data, $recipe->recipe_yields);

          // avanzar la hora para la siguiente elaboracion
          $current_date->addHours(1);

        } catch (\Exception $e) {
          echo "error elaborando {$product->product_name}: {$e->getMessage()}\n";
          break;
        }
      }
    }
  }

  /**
   * crear ventas aleatorias para un dia
   */
  private function createDailySales($products, $current_date): void
  {
    // numero de ventas para este dia (entre 50 y 70)
    $sales_count = rand(50, 70);

    for ($i = 0; $i < $sales_count; $i++) {
      try {
        // seleccionar producto aleatorio con stock
        $product = $products->random();

        if ($product->total_stock <= 0) {
          continue;
        }

        // obtener precio aleatorio del producto
        $price = $product->prices()->inRandomOrder()->first();

        if (!$price) {
          continue;
        }

        // determinar cantidad a comprar (entre 1 y 3 unidades del precio)
        $quantity = rand(1, 3);

        // calcular cantidad total de productos
        $total_products = $price->quantity * $quantity;

        // verificar si hay stock suficiente
        if ($product->total_stock < $total_products) {
          continue;
        }

        // preparar datos de la venta
        $sale_data = [
          'user_id' => null,
          'client_type' => Sale::CLIENT_TYPE_UNREGISTERED(),
          'sale_type' => Sale::SALE_TYPE_PRESENCIAL(),
          'sold_on' => $current_date->copy(),
          'payment_type' => 'efectivo',
          'total_price' => $price->price * $quantity,
          'products' => [
            [
              'product' => $product,           // objeto producto completo
              'selected_price_id' => $price->id, // id del precio seleccionado
              'sale_quantity' => $quantity,      // cantidad de unidades del precio
              'unit_price' => $price->price,     // precio unitario
              'subtotal_price' => $price->price * $quantity // subtotal
            ]
          ]
        ];

        // crear la venta
        $this->sale_service->createPresentialSale($sale_data);

        // avanzar 2 minutos
        $current_date->addMinutes(2);

      } catch (\Exception $e) {
        echo "error creando venta: {$e->getMessage()}\n";
        continue;
      }
    }
  }
}
