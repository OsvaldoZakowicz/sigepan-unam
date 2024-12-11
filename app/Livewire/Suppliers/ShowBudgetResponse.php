<?php

namespace App\Livewire\Suppliers;

use App\Models\Quotation;
use Illuminate\View\View;
use Livewire\Component;

/**
 * ver la respuesta de un proveedor a un presupuesto solicitado
 * todo: necesito la fecha de respuesta al presupuesto, es decir, la fecha en que
 * proporciona los precios y o actualiza los precios.
 */
class ShowBudgetResponse extends Component
{
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
    return view('livewire.suppliers.show-budget-response');
  }
}
