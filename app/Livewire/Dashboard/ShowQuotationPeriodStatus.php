<?php

namespace App\Livewire\Dashboard;

use App\Models\RequestForQuotationPeriod;
use Illuminate\View\View;
use Livewire\Component;

class ShowQuotationPeriodStatus extends Component
{
  // Intervalo de actualización en milisegundos (30 segundos)
  public $poolingInterval = 30000;

  /**
   * obtener periodos de presupuesto
   * con estado y presupuestos.
   */
  public function searchQuotationPeriods()
  {
    return RequestForQuotationPeriod::with(['quotations', 'preorder_period'])
      ->whereHas('status', function ($query) {
        $query->whereIn('status_code', ['0', '1']);
      })
      ->orderByRaw("FIELD(period_status_id,
          (SELECT id FROM period_statuses WHERE status_code = '1'),
          (SELECT id FROM period_statuses WHERE status_code = '0')
      )")
      ->get();
  }

  /**
   * obtener conteo de preordenes completadas
   * para cada periodo
   */
  public function getCompletedQuotationsCount($periodId): int
  {
    return RequestForQuotationPeriod::find($periodId)
      ->quotations()
      ->where('is_completed', true)
      ->count();
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    $quotation_periods = $this->searchQuotationPeriods();

    return view('livewire.dashboard.show-quotation-period-status', compact('quotation_periods'));
  }
}
