<?php

namespace App\Livewire\Store;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use Livewire\Component;

class Cart extends Component
{
  public Collection $cart;
  public $preference_id;

  public function mount()
  {
    // Verificar si existe un carrito en la sesion
    if (Session::has('cart')) {
      $this->cart = collect(Session::get('cart'));
    }
  }

  public function createPreference()
  {
    MercadoPagoConfig::setAccessToken(env('MERCADO_PAGO_ACCESS_TOKEN'));

    $client = new PreferenceClient();

    $items = [];
    $items = $this->cart->map(function ($cart_item) {
      return [
          "title" => $cart_item['product']->product_name,
          "quantity" => $cart_item['quantity'],
          "unit_price" => (float) $cart_item['product']->product_price
      ];
    });

    $preference = $client->create([
      "items"=> $items
    ]);

    $this->preference_id = $preference->id;
  }

  public function render()
  {
    $this->createPreference();
    return view('livewire.store.cart');
  }
}
