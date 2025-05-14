<?php

namespace App\Services\Sale;

use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class SaleService
{

  /**
   * Crear una nueva venta presencial
   * @param array $new_sale_data Array de datos de la venta
   * @return Sale
   * @throws Exception
   */
  public function createPresentialSale(array $new_sale_data): Sale
  {
    try {

      DB::beginTransaction();

      // venta
      $sale = Sale::create([
        'user_id'      => $new_sale_data['user_id'],
        'client_type'  => $new_sale_data['client_type'],
        'sale_type'    => $new_sale_data['sale_type'],
        'payment_type' => $new_sale_data['payment_type'],
        'total_price'  => $new_sale_data['total_price']
      ]);

      // productos vendidos
      foreach ($new_sale_data['products'] as $product) {
        $sale->products()->attach($product['product']->id, [
          'sale_quantity'  => $product['sale_quantity'],
          'unit_price'     => $product['unit_price'],
          'subtotal_price' => $product['subtotal_price'],
        ]);
      }

      // todo: movimiento de stock

      DB::commit();
      return $sale;

    } catch (\Exception $e) {

      DB::rollBack();
      throw $e;
    }
  }
}
