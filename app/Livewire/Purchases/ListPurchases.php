<?php

namespace App\Livewire\Purchases;

use App\Models\Purchase;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Component;

class ListPurchases extends Component
{
  use WithPagination;

  #[Url]
  public $search_purchase = '';

  /**
   * buscar compras
   */
  public function searchPurchases()
  {
    return Purchase::paginate(10);
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
    $this->reset(['search_product', 'tag_filter', 'in_store_filter']);
  }

  public function render()
  {
    $purchases = $this->searchPurchases();
    return view('livewire.purchases.list-purchases', compact('purchases'));
  }
}
