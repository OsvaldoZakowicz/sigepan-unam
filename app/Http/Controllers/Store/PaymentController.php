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
  public function cart_index(): View
  {
    // Verificar si hay un carrito en la sesión
    if (!Session::has('cart')) {
      return redirect()->route('store-store-index');
    }

    $cart = Session::get('cart');

    return view('store.cart-index', compact('cart'));
  }
}
