<?php

namespace App\Livewire\Suppliers;

use App\Jobs\ClosePreOrderPeriodJob;
use App\Jobs\NotifySuppliersRequestForPreOrderClosedJob;
use App\Jobs\NotifySuppliersRequestForPreOrderReceivedJob;
use App\Jobs\OpenPreOrderPeriodJob;
use App\Models\PreOrderPeriod;
use App\Services\Supplier\PreOrderPeriodService;
use App\Services\Supplier\QuotationPeriodService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\View\View;
use Livewire\WithPagination;
use Livewire\Component;

class ShowPreOrderPeriod extends Component
{
  // periodo de pre ordenes
  public $preorder_period;

  // ranking de presupuestos asociados
  public array | null $quotations_ranking = null;

  // posibles estados del periodo
  public int $scheduled;
  public int $opened;
  public int $closed;

  // estado del periodo
  public int $period_status;

  /**
   * boot de constantes
   * @param PreOrderPeriodService $pps servicio para pre ordenes
   * @return void
  */
  public function boot(PreOrderPeriodService $pps): void
  {
    // ids de estados
    $this->scheduled = $pps->getStatusScheduled();
    $this->opened = $pps->getStatusOpen();
    $this->closed = $pps->getStatusClosed();
  }

  /**
   * montar datos
   * @param int $id del periodo de pre orden
   * @return void
   */
  public function mount(QuotationPeriodService $qps, int $id): void
  {
    $this->preorder_period = PreOrderPeriod::findOrFail($id);

    /**
     * necesito crear nuevamente datos de ranking de presupuestos,
     * para obtener suministros y packs de interes. Siempre que
     * el periodo de pre orden parta de un periodo presupuestario
     */
    if ($this->preorder_period->quotation_period_id !== null) {
      // generar ranking inicial del periodo presupuestario
      $this->quotations_ranking = $qps->comparePricesBetweenQuotations($this->preorder_period->quotation_period_id);
    }
  }

  /**
   * abrir el periodo.
   * @return void
  */
  public function openPeriod(): void
  {
    $this->preorder_period->period_start_at = Carbon::now()->format('Y-m-d');
    $this->preorder_period->save();

    Bus::chain([
      OpenPreOrderPeriodJob::dispatch($this->preorder_period),
      NotifySuppliersRequestForPreOrderReceivedJob::dispatch($this->preorder_period),
    ]);
  }

  /**
   * cerrar el periodo.
   * @return void
  */
  public function closePeriod(): void
  {
    $this->preorder_period->period_end_at = Carbon::now()->format('Y-m-d');
    $this->preorder_period->save();

    Bus::chain([
      ClosePreOrderPeriodJob::dispatch($this->preorder_period),
      NotifySuppliersRequestForPreOrderClosedJob::dispatch($this->preorder_period),
    ]);
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    $preorders = $this->preorder_period
      ->pre_orders()
      ->orderBy('id','desc')
      ->paginate(10);

    return view('livewire.suppliers.show-pre-order-period', compact('preorders'));
  }
}
