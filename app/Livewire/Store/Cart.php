<?php

namespace App\Livewire\Store;

use App\Models\Order;
use App\Services\Sale\OrderService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Cart extends Component
{

  // orden (o pedido)
  public $order;

  // id de la preferencia de pagos para el boton de MP.
  public $preference_id;

  // servicio de ordenes
  protected OrderService $order_service;

  /**
   * montar datos
   * @param $id del pedido realizado
   * @return void
   */
  public function mount($id): void
  {
    $this->order = Order::findOrFail($id);

    // servicio de ordenes
    $this->order_service = new OrderService();
  }

  /**
   * Crear preferencia de pago para MP
   * @return void
   */
  public function createPreference(): void
  {
    // configurar preferencia de pago
    $new_preference = $this->order_service->createMercadoPagoPreference($this->order);

    if (isset($result['error'])) {
      //$this->error = $result['error'];
      return;
    }

    // guardar informacion del id de preferencia, proceder al pago
    $this->preference_id = $new_preference['preference_id'];
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    // disparar la creacion de preferencias
    $this->createPreference();

    return view('livewire.store.cart');
  }
}
