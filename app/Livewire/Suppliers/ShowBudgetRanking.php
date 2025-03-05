<?php

namespace App\Livewire\Suppliers;

use App\Models\RequestForQuotationPeriod;
use App\Models\PreOrderPeriod;
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
   * redirigir a la creacion de un periodo de pre ordenes
   * a partir de un periodo presupuestario.
   * siempre que no haya sido creado uno antes
   * @param int $id id del periodo presupuestario
   */
  public function createPreorders(int $id)
  {
    $quotations_period = RequestForQuotationPeriod::findOrFail($id);

    if (!PreOrderPeriod::where('quotation_period_id', $quotations_period->id)->exists()) {
      // redirigir a la creacion del periodo de pre ordenes
      return redirect()->route('suppliers-preorders-create', ['id' => $id]);
    }

    $this->dispatch('toast-event', toast_data: [
      'event_type'  =>  'info',
      'title_toast' =>  toastTitle('',true),
      'descr_toast' =>  'Este ranking de presupuestos ya tiene un periodo de pre ordenes creado.',
    ]);

    return;
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
