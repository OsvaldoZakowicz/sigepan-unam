<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
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
    if (!Session::has('cart')) {

      // Si no hay carrito, redirigir a la tienda
      return redirect()->route('store-store-index');
    }

    $cart = Session::get('cart');

    return view('store.cart-index', compact('cart'));
  }

  // * pago exitoso
  public function payment_success(Request $request): View
  {
    Log::info('Pago exitoso', $request->all());

    // limpiar carrito y regenerar sesión
    // todo: verificar
    Session::forget('cart');
    Session::regenerate();

    // todo: manejar datos del pago
    // todo: manejar pedido
    // todo: falta algo mas?

    //dd('exitoso:', 'carrito;', Session::get('cart'), $request->all());

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
