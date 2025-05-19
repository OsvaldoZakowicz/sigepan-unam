<?php

namespace App\Livewire\Store;

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
  // carrito de compras
  public Collection $cart;
  public float $total_price = 0;

  // id de la preferencia de pagos para el boton de MP.
  public $preference_id;

  // servicio de ordenes
  protected OrderService $order_service;

  /**
   * montar datos
   * @return void
   */
  public function mount(): void
  {
    // Verificar si existe un carrito en la sesion
    if (Session::has('products_for_cart')) {

      // recuperar carrito
      // * renombrando a 'cart'
      $this->cart = collect(Session::get('products_for_cart'));

      // precio total para la vista
      $this->total_price = $this->cart->reduce(function ($carry, $product) {
        return $carry + $product['subtotal_price'];
      }, 0);

      // servicio de ordenes
      $this->order_service = new OrderService();
    } else {
      return;
    }
  }

  /**
   * Crear preferencia de pago para MP
   *
   * @return void
   */
  public function createPreference(): void
  {

    // todo: crear orden con estado de pago pendiente

    // configurar preferencia de pago
    $new_preference = $this->order_service->createMercadoPagoPreference($this->cart);

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
