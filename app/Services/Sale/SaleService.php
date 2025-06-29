<?php

namespace App\Services\Sale;

use App\Models\DatoNegocio;
use App\Models\Sale;
use App\Models\Order;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Services\Stock\StockService;
use App\Models\Price;
use Illuminate\Http\Request;
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
   * crear una venta desde la web.
   * registra la venta de un pedido ya existente, registra el pago del pedido
   * NO reduce el stock ya que no crea movimiento de stock.
   * @param string $order_code codigo de orden
   * @param Request $request request completo
   * @return Sale
   * @throws Exception
   */
  public function createOnlineSale(string $order_code, Request $request): Sale
  {
    try {

      // obtener la orden pagada
      $order = Order::where('order_code', $order_code)->first();

      // Actualizar estado del pago de la orden
      $order->payment_status = Order::ORDER_PAYMENT_STATUS_APROBADO();
      $order->save();

      // a partir de datos del request, crear un json 'full_response' para mercado pago
      $mp_response = [
        'mp' => [
          'collection_id'       => $request->collection_id,
          'collection_status'   => $request->collection_status,
          'payment_id'          => $request->payment_id,
          'status'              => $request->status,
          'external_reference'  => $request->external_reference, // referencia externa, codigo de orden en este caso
          'payment_type'        => $request->payment_type,
          'merchant_order_id'   => $request->merchant_order_id,
          'preference_id'       => $request->preference_id,
          'site_id'             => $request->site_id,
          'processing_mode'     => $request->processing_mode,
          'merchant_account_id' => $request->merchant_account_id
        ]
      ];

      DB::beginTransaction();

      // Crear venta
      $sale = Sale::create([
        'user_id'      => $order->user_id,
        'order_id'     => $order->id,
        'client_type'  => Sale::CLIENT_TYPE_REGISTERED(),
        'sale_type'    => Sale::SALE_TYPE_WEB(),
        'sold_on'      => now()->format('d-m-Y H:i'),
        'payment_type' => 'mercado pago',
        'total_price'  => (float) $order->total_price,
        'full_response' => json_encode($mp_response),
      ]);

      // relacionar productos a la venta
      foreach ($order->products as $product) {
        $sale->products()->attach($product->id, [
          'sale_quantity'  => $product->pivot->order_quantity,
          'unit_price'     => $product->pivot->unit_price,
          'subtotal_price' => $product->pivot->subtotal_price,
          'details'        => $product->pivot->details,
        ]);
      }

      DB::commit();
      return $sale;
    } catch (\Exception $e) {

      DB::rollBack();
      throw $e;
    }
  }

  /**
   * A partir de una venta, obtener los datos de presentacion
   * para un comprobante en PDF.
   * @param Sale $sale
   * @return array ['header' => [], 'detail' => []]
   */
  public function generateSaleData(Sale $sale): array
  {
    $sale->load(['user' => function ($query) {
      $query->withTrashed()
        ->with([
          'profile' => function ($q) {
            $q->withTrashed()
              ->with([
                'address' => function ($q) {
                  $q->withTrashed();
                }
              ]);
          }
        ]);
    }])->first();

    if ($sale->user) {
      $usr = $sale->user;
      $username = $usr->name;
      $email = $usr->email;
    } else {
      $username = '-';
      $email = '-';
    }

    if ($sale->user->profile) {
      $pr = $sale->user->profile;
      $fullname = $pr->first_name . ', ' . $pr->last_name;
      $dni = $pr->dni;
      $contact = $pr->phone_number;
    } else {
      $fullname = '-';
      $contact = '-';
      $dni = '-';
    }

    if ($sale->user->profile->address) {
      $adr = $sale->user->profile->address;
      $full_address = $adr->street . ', numero ' . $adr->number . ', ciudad: '
        . $adr->city . ', CP' . $adr->postal_code;
    } else {
      $full_address = '-';
    }

    $client = [
      'username'        =>  $username,
      'email'           =>  $email,
      'full_name'       =>  $fullname,
      'contact'         =>  $contact,
      'dni'             =>  $dni,
      'full_address'    =>  $full_address,
      'account_status'  =>  $sale->user->trashed() ? 'cuenta borrada' : 'usuario activo'
    ];

    $sale_data = [
      'id'              => $sale->id,
      'fecha'           => $sale->sold_on->format('d-m-Y H:i'),
      'establecimiento' => DatoNegocio::obtenerTodos(),
      'cliente'         => $client,
      'forma_de_pago'   => $sale->payment_type,
      'total'           => number_format($sale->total_price, 2),
    ];

    $sale_detail = [];
    foreach ($sale->products as $key => $product) {
      array_push($sale_detail, [
        'nro' => $key + 1,
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
