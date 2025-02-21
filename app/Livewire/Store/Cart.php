<?php

namespace App\Livewire\Store;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use Livewire\Component;

class Cart extends Component
{
  public Collection $cart;
  public float $total_price;

  public $preference_id;

  /**
   * montar datos
   * @return void
   */
  public function mount(): void
  {
    // Verificar si existe un carrito en la sesion
    if (Session::has('cart')) {

      // recuperar carrito
      $this->cart = collect(Session::get('cart'));

      // precio total
      $this->total_price = $this->cart->reduce(function ($carry, $cart_item) {
        return $carry + $cart_item['subtotal'];
      }, 0);

    }
  }

  /**
   * crear preferencia de pago para MP
   * @return void
   */
  public function createPreference(): void
  {
    // carrito vacio
    if ($this->cart->isEmpty()) {
      return;
    }

    // configuracion
    MercadoPagoConfig::setAccessToken(env('MERCADO_PAGO_ACCESS_TOKEN'));

    // preparar items del carrito
    $items = $this->cart->map(function ($cart_item) {
      return [
        "id"          => $cart_item['product']->id,
        "title"       => $cart_item['product']->product_name,
        "quantity"    => (int) $cart_item['quantity'],
        "unit_price"  => (float) $cart_item['product']->product_price,
        "currency_id" => "ARS",
      ];
    });

    // nueva preferencia
    $client = new PreferenceClient();

    try {

      // completar preferencia
      $preference = $client->create([
        "items"=> $items,
        "back_urls" => [
            "success" => route('store-store-payment-success'),
            "failure" => route('store-store-payment-failure'),
            "pending" => route('store-store-payment-pending')
        ],
        "auto_return" => "approved",
        "statement_descriptor" => "SiGePAN",
        "external_reference" => Auth::check() ? Auth::id() : session()->getId(),
      ]);

      // publicar id de preferencia
      $this->preference_id = $preference->id;

    } catch (\MercadoPago\Exceptions\MPApiException $e) {

      dd($e->getMessage(), $e->getCode());
    }
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
