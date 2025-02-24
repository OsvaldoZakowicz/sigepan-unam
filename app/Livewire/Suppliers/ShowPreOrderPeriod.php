<?php

namespace App\Livewire\Suppliers;

use App\Models\PreOrderPeriod;
use App\Services\Supplier\PreOrderPeriodService;
use Illuminate\Support\Carbon;
use Livewire\Component;

class ShowPreOrderPeriod extends Component
{
  public $preorder_period;
  public $preorders;

  // posibles estados del periodo
  public int $scheduled;
  public int $opened;
  public int $closed;

  // estado del periodo
  public int $period_status;

  /**
   * boot de constantes
   * @param $qps quotation period service
   * @return void
  */
  public function boot(PreOrderPeriodService $pps): void
  {
    // ids de estados
    $this->scheduled = $pps->getStatusScheduled();
    $this->opened = $pps->getStatusOpen();
    $this->closed = $pps->getStatusClosed();
  }

  public function mount($id)
  {
    $this->preorder_period = PreOrderPeriod::findOrFail($id);
    $this->preorders = $this->preorder_period->pre_orders;
  }

  /**
   * abrir el periodo.
   * @return void
  */
  public function openPeriod(): void
  {
    //todo: manejar con job para apertura
    //todo: incluir job para notificar via email
    $this->preorder_period->period_start_at = Carbon::now()->format('Y-m-d');
    $this->preorder_period->period_status_id = 2; //abierto
    $this->preorder_period->save();
  }

  /**
   * cerrar el periodo.
   * @return void
  */
  public function closePeriod(): void
  {
    //todo: manejar con job para cierre
    //todo: incluir job para notificar via email
    $this->preorder_period->period_end_at = Carbon::now()->format('Y-m-d');
    $this->preorder_period->period_status_id = 3; //cerrado
    $this->preorder_period->save();
  }

  public function render()
  {
    return view('livewire.suppliers.show-pre-order-period');
  }
}
