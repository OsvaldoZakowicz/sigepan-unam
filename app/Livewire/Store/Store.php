<?php

namespace App\Livewire\Store;

use App\Models\Product;
use Illuminate\View\View;
use Livewire\WithPagination;
use Livewire\Component;

class Store extends Component
{

  use WithPagination;

  /**
   * montar datos
   * @return void
  */
  public function mount(): void
  {
  }

  /**
   * agregar producto al carrito
   * @param Product $product
   * @return void
  */
  public function addToCart(Product $product): void
  {
    return;
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
