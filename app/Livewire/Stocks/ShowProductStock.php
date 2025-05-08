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

  #[Url]
  public $search_stock = '';

  #[Url]
  public $order_stock = 'id';

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

    // verificar si hay un ID pendiente en la sesion
    // en caso de que se llegue a esta vista por redireccion desde la lista de existencias
    if ($stock_id = session('pending_stock_id')) {
      $stock = Stock::find($stock_id)
        ->with('stock_movements')
        ->first();
      if ($stock) {
        $this->openMovementsModal($stock);
      }
    }
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
      ->with(['stock_movements', 'recipe'])
      ->when($this->search_stock, function ($query) {
        $query->where('id', 'like', '%' . $this->search_stock . '%')
          ->orWhere('lote_code', 'like', '%' . $this->search_stock . '%');
      })
      ->when($this->order_stock, function ($query) {
        $query->orderBy($this->order_stock, 'desc');
      })
      ->paginate(10);
  }

  /**
   * resetear la paginacion
   * @return void
   */
  public function resetPagination(): void
  {
    $this->resetPage();
  }

  /**
   * limpiar campos de busqueda
   * @return void
   */
  public function resetSearchInputs(): void
  {
    $this->reset(['search_stock', 'order_stock']);
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
