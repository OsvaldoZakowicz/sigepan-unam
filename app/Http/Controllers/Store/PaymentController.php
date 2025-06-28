<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\DatoTienda;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Services\Sale\SaleService;
use Illuminate\View\View;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
  // * mostrar carrito y preparar pago
  public function cart_index($id)
  {
    return view('store.cart-index', ['id' => $id]);
  }

  /**
   * * pago exitoso
   * @param Request $request respuesta de MP.
   * @return View
   */
  public function payment_success(Request $request): View
  {
    // servicio de ventas
    $sale_service = new SaleService();

    try {

      // crear venta
      $sale = $sale_service->createOnlineSale($request->external_reference, $request);

      return view('store.payment-success');
    } catch (\Exception $e) {

      Log::error('Error general al crear la venta web:', [
        'message' => $e->getMessage(),
        'code' => $e->getCode()
      ]);

      session()->flash('operation-info', 'Ocurrió un error inesperado. El pago fue exitoso, pero no pudimos registrarlo!, por favor acerque el comprobante de pago al momento de recibir o retirar su pedido');

      return view('store.payment-success');
    }
  }

  /**
   * * pago fallido
   * cuando en la vista de MP el cliente pulsa "volver al sitio". No hace el pago
   * @param Request $request respuesta de MP.
   * @return View
   */
  public function payment_failure(Request $request): View
  {
    // Log::info('Pago fallido', $request->all());
    // dd('fallido: ', $request->all());

    $datos_tienda_pago = DatoTienda::obtenerValor('tiempo_espera_pago');
    return view('store.payment-failure', compact('datos_tienda_pago'));
  }

  // * pago pendiente
  public function payment_pending(Request $request): View
  {
    //Log::info('Pago pendiente', $request->all());
    //dd('pendiente: ',$request->all());

    return view('store.payment-pending');
  }
}
