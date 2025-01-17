<?php

namespace App\Livewire\Suppliers;

use App\Models\Quotation;
use Illuminate\View\View;
use Livewire\WithPagination;
use Livewire\Component;

class ShowBudgetResponse extends Component
{
  use WithPagination;

  public $quotation;

  /**
   * montar datos
   * @param int $id id de un presupuesto
   * @return void
  */
  public function mount(int $id): void
  {
    $this->quotation = Quotation::findOrFail($id);
  }

  /**
   * obtener presupuesto en pdf
   * @return void
  */
  public function export(): void
  {
    // todo
  }

  /**
   * renderizar vista
   * @return View
  */
  public function render(): View
  {
    $provisions = $this->quotation->provisions()->paginate(8);

    return view('livewire.suppliers.show-budget-response', compact('provisions'));
  }
}
