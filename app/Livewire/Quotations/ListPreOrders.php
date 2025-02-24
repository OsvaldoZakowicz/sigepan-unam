<?php

namespace App\Livewire\Quotations;

use App\Models\Supplier;
use App\Models\PreOrder;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Livewire\Component;

class ListPreOrders extends Component
{
  use WithPagination;

  public Supplier $supplier;

  /**
   * boot de datos
   * @return void
  */
  public function boot(): void
  {
    // cada supplier tiene un usuario, y esta en sesion (Auth::user())
    $this->supplier = Auth::user()->supplier;
  }

  /**
   * buscar preordenes
   * @return mixed
  */
  public function searchPreorders()
  {
    // cada quotation tiene un supplier (proveedor) y un period
    // obtener la lista de quotations que tiene el proveedor en sesion
    $preorders = PreOrder::with(['supplier', 'pre_order_period'])
      ->where('supplier_id', $this->supplier->id)
      ->orderBy('id', 'desc')
      ->paginate(10);

    return $preorders;
  }

  public function render()
  {
    $preorders = $this->searchPreorders();
    return view('livewire.quotations.list-pre-orders', compact('preorders'));
  }
}
