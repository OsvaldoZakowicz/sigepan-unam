<?php

namespace App\Livewire\Stocks;

use App\Models\Product;
use Illuminate\View\View;
use Livewire\Component;

class ShowProduct extends Component
{
  public Product $product;

  /**
   * montar datos
   * @return void
  */
  public function mount(int $id): void
  {
    $this->product = Product::findOrFail($id);
  }

  /**
   * renderizar vista
   * @return View
  */
  public function render(): View
  {
    return view('livewire.stocks.show-product');
  }
}
