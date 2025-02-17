<?php

namespace App\Livewire\Store;

use App\Models\Product;
use App\Models\Tag;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Component;

class Store extends Component
{

  use WithPagination;

  // busquedas
  #[Url]
  public $search_products = '';

  #[Url]
  public $search_by_tag = '';

  public Collection $cart;
  public bool $show_cart_modal = false;
  public float $cart_total = 0;
  public int $cart_total_items = 0;
  public Collection $tags;

  /**
   * boot de constantes
   * @return void
   */
  public function boot(): void
  {
    $this->tags = Tag::all();
  }

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
   * vaciar el carrito
   * @return void
   */
  public function resetCart(): void
  {
    $this->cart = collect();
    $this->cart_total_items = 0;
  }

  /**
   * intercepta cualquier cambio en las cantidades del carrito
   * @param string $name nombre del input cantidad del carrito para cada item
   * @param $value valor del input
   * @return void
   */
  public function updated($name, $value): void
  {
    if (str_starts_with($name, 'cart.') && str_ends_with($name, '.quantity')) {
      $parts = explode('.', $name);
      $index = $parts[1];
      $product_id = $this->cart[$index]['id'];

      // Validar que sea numérico y convertir a entero
      if (!is_numeric($value) || (int) $value <= 0) {
        $this->fixCartItemQuantity($index, $product_id);
        return;
      }
    }
  }

  /**
   * manejar la corrección de valores invalidos
   * @param $index
   * @param $product_id
   * @return void
   */
  private function fixCartItemQuantity($index, $product_id): void
  {
    $currentItem = $this->cart->firstWhere('id', $product_id);
    // Asegurar que sea numérico y al menos 1
    $valid_quantity = is_numeric($currentItem['quantity']) ?
      max(1, (int) $currentItem['quantity']) : 1;

    $this->cart = $this->cart->map(function ($item, $key) use ($index, $product_id, $valid_quantity) {
      if ($key == $index && $item['id'] === $product_id) {
        return [
          'id' => $item['id'],
          'product' => $item['product'],
          'quantity' => $valid_quantity,
          'subtotal' => $item['product']->product_price * $valid_quantity
        ];
      }
      return $item;
    })->values();

    $this->calculateTotal();
    $this->dispatch('quantity-error', 'Por favor, ingrese solo números mayores a 0');
  }

  /**
   * Actualiza la cantidad de un producto en el carrito y recalcula el subtotal
   * @param int $product_id ID del producto a actualizar
   * @param int $quantity Nueva cantidad del producto
   * @return void
   */
  public function updateQuantity($product_id, $quantity): void
  {
    // Validar que sea numérico
    if (!is_numeric($quantity)) {
      return;
    }

    $quantity = max(1, (int) $quantity);

    $this->cart = $this->cart->map(function($item) use ($product_id, $quantity) {

      if ($item['id'] === $product_id) {
        return [
          'id' => $item['id'],
          'product' => $item['product'],
          'quantity' => $quantity,
          'subtotal' => $item['product']->product_price * $quantity
        ];
      }

      return $item;
    })->values();

    $this->calculateTotal();
  }

  /**
   * Elimina un objeto del carrito de compras.
   * @param int $product_id id del producto a eliminar
   * @return void
   */
  public function removeFromCart(int $product_id): void
  {
    $this->cart = $this->cart->filter(function ($item) use ($product_id) {
      return $item['id'] !== $product_id;
    })->values();

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
   * reiniciar paginacion
   * @return void
   */
  public function resetPagination(): void
  {
    $this->resetPage();
  }

  /**
   * limpiar filtros de busqueda
   * @return void
   */
  public function resetSearchInputs(): void
  {
    $this->reset(['search_products', 'search_by_tag']);
    $this->resetPagination();
  }

  /**
   * buscar productos
   * @return mixed
   */
  public function searchProducts()
  {
    $products = Product::with('tags')
      ->where('product_in_store', true)
      ->when($this->search_products, function ($query) {
        $query->where('product_name', 'like', '%' . $this->search_products . '%')
          ->orWhere('product_price', '<=', (float) $this->search_products);
      })
      ->when($this->search_by_tag, function ($query) {
        $query->whereHas('tags', function ($q) {
          $q->where('tags.id', (int) $this->search_by_tag);
        });
      })
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
