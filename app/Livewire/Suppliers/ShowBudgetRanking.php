<?php

namespace App\Livewire\Suppliers;

use App\Models\RequestForQuotationPeriod;
use App\Services\Supplier\QuotationPeriodService;
use Illuminate\View\View;
use Livewire\Component;

class ShowBudgetRanking extends Component
{
  // periodo
  public RequestForQuotationPeriod $period;

  // comparativa de precios
  public array $quotations_ranking;

  /**
   * montar datos
   * @param $id id del periodo
   * @return void
   */
  public function mount(QuotationPeriodService $qps, int $id)
  {
    $this->period = RequestForQuotationPeriod::findOrFail($id);

    // todo: es necesario usar paginacion
    $this->quotations_ranking = $qps->comparePricesBetweenQuotations($this->period->id);
  }

  /**
   * renderizar vista
   * @return view
   */
  public function render(): View
  {
    return view('livewire.suppliers.show-budget-ranking');
  }
}
