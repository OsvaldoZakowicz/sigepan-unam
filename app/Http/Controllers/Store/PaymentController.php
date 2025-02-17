<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
  public function success(Request $request)
  {
    try {
      // Loguear los datos recibidos de Mercado Pago
      Log::info('Payment Success Callback', [
        'payment_id' => $request->get('payment_id'),
        'status' => $request->get('status'),
        'external_reference' => $request->get('external_reference'),
        'merchant_order_id' => $request->get('merchant_order_id')
      ]);

      // Aquí podrías:
      // 1. Actualizar el estado de la orden en tu base de datos
      // 2. Enviar email de confirmación
      // 3. Limpiar el carrito
      // 4. Etc.

      // Redirigir a una vista de éxito con mensaje
      return redirect()
        ->route('store.index')
        ->with('success', '¡Pago realizado con éxito! Tu pedido está siendo procesado.');
    } catch (\Exception $e) {
      Log::error('Error en callback de éxito: ' . $e->getMessage());

      return redirect()
        ->route('store.index')
        ->with('error', 'Hubo un problema procesando tu pago. Por favor, contacta a soporte.');
    }
  }
}
