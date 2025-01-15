<?php

namespace App\Livewire\Quotations;

use App\Models\Quotation;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\WithPagination;
use Livewire\Component;

class ListQuotations extends Component
{
  use WithPagination;

  /**
   * todo: vista responsiva
   */

  /**
   * renderizar vista
   * @return view
  */
  public function render(): View
  {
    // cada supplier tiene un usuario, y esta en sesion (Auth::user())
    $supplier_id = Auth::user()->supplier->id;

    // cada quotation tiene un supplier (proveedor)
    // obtener la lista de quotations que tiene el proveedor en sesion
    $quotations = Quotation::with('supplier')
      ->where('supplier_id', $supplier_id)
      ->paginate(10);

    return view('livewire.quotations.list-quotations', compact('quotations'));
  }
}
