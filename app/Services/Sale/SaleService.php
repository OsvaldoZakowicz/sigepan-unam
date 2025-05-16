<?php

namespace App\Services\Sale;

use App\Models\Sale;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Services\Stock\StockService;
use App\Models\Price;
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

      // Crear venta
      $sale = Sale::create([
        'user_id'      => $new_sale_data['user_id'],
        'client_type'  => $new_sale_data['client_type'],
        'sale_type'    => $new_sale_data['sale_type'],
        'sold_on'      => $new_sale_data['sold_on'],
        'payment_type' => $new_sale_data['payment_type'],
        'total_price'  => $new_sale_data['total_price']
      ]);

      // Productos vendidos y movimientos de stock
      $stock_service = new StockService();

      foreach ($new_sale_data['products'] as $product) {

        // Preparar detalle
        $product_price = $product['product']->prices->find($product['selected_price_id']);
        $details = $product_price->description . ' (' . $product_price->quantity . ') a $' . $product_price->price;

        // Relacionar producto con la venta
        $sale->products()->attach($product['product']->id, [
          'sale_quantity'  => $product['sale_quantity'],
          'unit_price'     => $product['unit_price'],
          'subtotal_price' => $product['subtotal_price'],
          'details'        => $details,
        ]);

        // Obtener el precio seleccionado y calcular cantidad total a descontar
        $selected_price = Price::find($product['selected_price_id']);
        $total_units_to_deduct = $selected_price->quantity * $product['sale_quantity'];

        // Obtener stocks disponibles ordenados por fecha de vencimiento
        $available_stocks = Stock::where('product_id', $product['product']->id)
          ->where('quantity_left', '>', 0)
          ->orderBy('expired_at')
          ->get();

        $remaining_units = $total_units_to_deduct;

        foreach ($available_stocks as $stock) {
          if ($remaining_units <= 0) break;

          // Calcular cuántas unidades podemos tomar de este stock
          $units_to_deduct = min($remaining_units, $stock->quantity_left);

          // Registrar movimiento negativo (venta)
          $stock_service->registerMovement(
            $stock->id,
            -$units_to_deduct,
            StockMovement::MOVEMENT_TYPE_VENTA(),
            $sale->id, // id de la venta
            get_class($sale) // App\Models\Sale
          );

          $remaining_units -= $units_to_deduct;
        }

        // Si quedaron unidades sin descontar, no hay suficiente stock
        if ($remaining_units > 0) {

          throw new \Exception(
            "Stock insuficiente para el producto {$product['product']->product_name}. " .
              "Faltan {$remaining_units} unidades."
          );
        }
      }

      DB::commit();
      return $sale;

    } catch (\Exception $e) {

      DB::rollBack();
      throw $e;
    }
  }

  /**
   * A partir de una venta, obtener los datos de presentancion
   * para un comprobante en PDF.
   * @param Sale $sale
   * @return array ['header' => [], 'detail' => []]
   */
  public function generateSaleData(Sale $sale): array
  {
    $client = ($sale->user()->exists())
      ? $sale->user->name . ' - ' . $sale->user->email
      : $sale->client_type;

    $sale_data = [
      'id'              => $sale->id,
      'fecha'           => $sale->sold_on->format('d-m-Y H:i'),
      'establecimiento' => '', // todo
      'cliente'         => $client,
      'forma_de_pago'   => $sale->payment_type,
      'total'           => number_format($sale->total_price, 2),
    ];

    $sale_detail = [];
    foreach ($sale->products as $key => $product) {
      array_push($sale_detail, [
        'nro' => $key+1,
        'producto' => $product->product_name,
        'detalle'  => $product->pivot->details,
        'cantidad' => $product->pivot->sale_quantity,
        'precio_unitario' => number_format($product->pivot->unit_price, 2),
        'subtotal'        => number_format($product->pivot->subtotal_price, 2)
      ]);
    }

    return [
      'header' => $sale_data,
      'detail' => $sale_detail,
    ];
  }
}
