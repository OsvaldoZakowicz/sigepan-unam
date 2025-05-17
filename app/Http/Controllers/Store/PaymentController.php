<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\View\View;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
  // * mostrar carrito y preparar pago
  public function cart_index()
  {
    // Verificar si hay un carrito en la sesión
    if (!Session::has('products_for_cart')) {

      // Si no hay carrito, redirigir a la tienda
      return redirect()->route('store-store-index');
    }

    $cart = Session::get('products_for_cart');

    return view('store.cart-index', compact('cart'));
  }

  // * pago exitoso
  /**
   * pago exitoso
   * Formato de respuesta:
   *
   * "collection_id" => "102720537383" // * Numero de transaccion
   * "collection_status" => "approved" // * estado del pago
   * "payment_id" => "102720537383"    // * Numero de transaccion
   * "status" => "approved"            // * estado del pago
   * "external_reference" => "10"      // * referencia externa
   * "payment_type" => "account_money" // * tipo de pago
   * "merchant_order_id" => "28733638120"
   * "preference_id" => "2272238706-4d152f14-0bd7-43e2-8c0f-a8ac472f4361"
   * "site_id" => "MLA"
   * "processing_mode" => "aggregator"
   * "merchant_account_id" => "null"
   *
   * @param Request $request respuesta de MP.
   * @return View
   */
  public function payment_success(Request $request): View
  {
    // Obtener carrito
    // [['id' => 1, 'product' => App\Models\Product, 'quantity' => 2, 'subtotal' => 100], ...]
    $cart = Session::get('cart');

    $order_status_id = OrderStatus::where('status', OrderStatus::PENDIENTE)->first()->id;
    $user_id = Auth::id();
    $order_origin = Order::WEB;
    $total_price = $cart->reduce(fn ($carry, $item) => $carry + $item['subtotal'], 0);

    // crear pedido
    $order = Order::create([
      'order_code'      => 'ORD-' . time(),
      'order_status_id' => $order_status_id, // pendiente
      'user_id'         => $user_id, // usuario logueado
      'employee_id'     => null, // empleado logueado
      'order_origin'    => $order_origin,
      'total_price'     => $total_price,
    ]);

    // agregar productos al pedido
    foreach ($cart as $item) {
      $order->products()->attach($item['id'], [
        'quantity'       => $item['quantity'],
        'unit_price'     => $item['product']->product_price,
        'subtotal_price' => $item['subtotal'],
      ]);
    }

    // crear venta
    $sale = $order->sale()->create([
      'payment_type'       => 'mercado pago',
      'payment_id'         => $request->payment_id,
      'status'             => $request->status,
      'external_reference' => $request->external_reference,
      'merchant_order_id'  => $request->merchant_order_id,
      'total_price'        => $total_price,
      'full_response'      => json_encode($request->all()),
    ]);

    // limpiar carrito y regenerar sesión
    Session::forget('cart');
    Session::regenerate();

    return view('store.payment-success');
  }

  // * pago fallido
  public function payment_failure(Request $request): View
  {
    Log::info('Pago fallido', $request->all());

    dd('fallido: ', $request->all());

    return view('store.payment-failure');
  }

  // * pago pendiente
  public function payment_pending(Request $request): View
  {
    Log::info('Pago pendiente', $request->all());

    dd('pendiente: ',$request->all());

    return view('store.payment-pending');
  }
}
