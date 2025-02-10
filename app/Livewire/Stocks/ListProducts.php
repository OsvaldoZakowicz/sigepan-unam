<?php

namespace App\Livewire\Stocks;

use App\Models\Product;
use Illuminate\View\View;
use Livewire\WithPagination;
use Livewire\Component;

class ListProducts extends Component
{
  use WithPagination;

  /**
   * buscar productos
   * @return mixed
  */
  public function searchProducts()
  {
    $products = Product::paginate(10);

    return $products;
  }

  /**
   * renderizar vista
   * @return View
  */
  public function render(): View
  {
    $products = $this->searchProducts();
    return view('livewire.stocks.list-products', compact('products'));
  }
}
