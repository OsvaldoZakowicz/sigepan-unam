<?php

namespace App\Livewire\Store;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\WithPagination;
use Livewire\Component;

class Store extends Component
{

  use WithPagination;

  public Collection $cart;
  public bool $show_cart_modal = false;
  public float $cart_total = 0;
  public int $cart_total_items = 0;

  /**
   * montar datos
   * @return void
   */
  public function mount(): void
  {
    $this->cart = collect();
  }

  /**
   * Muestra el modal del carrito de compras
   * estableciendo la propiedad show_cart_modal en true
   * @return void
   */
  public function showCartModal(): void
  {
    $this->show_cart_modal = true;
  }

  /**
   * agregar un item producto al carrito
   * @param Product $product
   * @return void
  */
  public function addToCart(Product $product): void
  {
    if (!$this->cart->contains('id', $product->id)) {

      $this->cart->push([
        'id' => $product->id,
        'product' => $product,
        'quantity' => 1,
        'subtotal' => $product->product_price
      ]);

      $this->cart_total_items++;
    }

    $this->calculateTotal();
    $this->showCartModal();
  }

  /**
   * Actualiza la cantidad de un producto en el carrito y recalcula el subtotal
   * @param int $productId ID del producto a actualizar
   * @param int $quantity Nueva cantidad del producto
   * @return void
  */
  public function updateQuantity($productId, $quantity): void
  {
    if ($quantity > 0) {
      $this->cart = $this->cart->map(function ($item) use ($productId, $quantity) {

        if ($item['id'] === $productId) {
          $item['quantity'] = $quantity;
          $item['subtotal'] = $item['product']->product_price * $quantity;
        }

        return $item;
      });
      $this->calculateTotal();
    }
  }

  /**
   * Elimina un objeto del carrito de compras.
   * @param int $key del producto que se desea eliminar del carrito
   * @return void
   */
  public function removeFromCart(int $key): void
  {
    $this->cart->forget($key);
    $this->cart_total_items--;
    $this->calculateTotal();
  }

  /**
   * Calcula el total del carrito sumando los subtotales de todos los productos
   * @return void
   */
  private function calculateTotal(): void
  {
    $this->cart_total = $this->cart->sum('subtotal');
  }

  /**
   * buscar productos
   * @return mixed
   */
  public function searchProducts()
  {
    $products = Product::where('product_in_store', true)
      ->orderBy('id', 'desc')
      ->paginate(10);

    return $products;
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    $products = $this->searchProducts();
    return view('livewire.store.store', compact('products'));
  }
}
