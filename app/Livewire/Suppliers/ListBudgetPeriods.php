<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RequestForQuotationPeriod;

/**
 * * listar periodos de peticion de presupuestos.
 */
class ListBudgetPeriods extends Component
{
  use WithPagination;

  public function render()
  {
    $periods = RequestForQuotationPeriod::paginate(10);

    return view('livewire.suppliers.list-budget-periods', compact('periods'));
  }
}
