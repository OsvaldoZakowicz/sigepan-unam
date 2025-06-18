<?php

namespace App\Livewire\Dashboard;

use App\Models\PreOrderPeriod;
use Illuminate\View\View;
use Livewire\Component;

class ShowPreOrderPeriodStatus extends Component
{

  // Intervalo de actualización en milisegundos (30 segundos)
  public $poolingInterval = 30000;

  /**
   * obtener periodos de pre orden
   * con estado y preordenes
   */
  public function searchPreOrderPeriods()
  {
    return PreOrderPeriod::with(['status', 'pre_orders'])
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
  public function getCompletedPreOrdersCount($periodId): int
  {
    return PreOrderPeriod::find($periodId)
      ->pre_orders()
      ->where('is_completed', true)
      ->count();
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    $preorder_periods = $this->searchPreOrderPeriods();

    return view('livewire.dashboard.show-pre-order-period-status', compact('preorder_periods'));
  }
}
