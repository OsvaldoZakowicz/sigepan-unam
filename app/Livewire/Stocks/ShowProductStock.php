<?php

namespace App\Livewire\Stocks;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\View\View;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Component;

class ShowProductStock extends Component
{
  use WithPagination;

  public ?Product $product;

  // Propiedades para el modal
  public bool $show_movements_modal = false;
  public ?Stock $selected_stock = null;

  /**
   * montar datos
   * @param int $id del producto
   * @return void
   */
  public function mount(int $id): void
  {
    $this->product = Product::findOrFail($id);
  }

  /**
   * Abrir modal de movimientos
   * @param Stock $stock
   */
  public function openMovementsModal(Stock $stock): void
  {
    $this->selected_stock = $stock;
    $this->show_movements_modal = true;
  }

  /**
   * buscar stock especifico
   */
  private function searchProductStock()
  {
    return $this->product->stocks()
      ->with(['stock_movements'])
      ->orderBy('id', 'desc')
      ->paginate(10);
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    $product_stocks = $this->searchProductStock();
    return view('livewire.stocks.show-product-stock', compact('product_stocks'));
  }
}
