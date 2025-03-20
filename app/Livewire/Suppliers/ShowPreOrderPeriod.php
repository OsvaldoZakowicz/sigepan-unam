<?php

namespace App\Livewire\Suppliers;

use App\Jobs\ClosePreOrderPeriodJob;
use App\Jobs\NotifySuppliersRequestForPreOrderClosedJob;
use App\Jobs\NotifySuppliersRequestForPreOrderReceivedJob;
use App\Jobs\OpenPreOrderPeriodJob;
use App\Jobs\SendEmailJob;
use App\Mail\ClosePreOrderPeriod;
use App\Models\PreOrderPeriod;
use App\Models\User;
use App\Services\Supplier\PreOrderPeriodService;
use App\Services\Supplier\QuotationPeriodService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\View\View;
use App\Models\PreOrder;
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

  // posibles estados de preorden
  public string $status_pending;
  public string $status_approved;
  public string $status_rejected;

  /**
   * boot de constantes
   * @param PreOrderPeriodService $pps servicio para pre ordenes
   * @return void
   */
  public function boot(PreOrderPeriodService $pps): void
  {
    // posibles estados del periodo
    $this->scheduled = $pps->getStatusScheduled();
    $this->opened = $pps->getStatusOpen();
    $this->closed = $pps->getStatusClosed();

    // posibles estados de preorden
    $this->status_pending = PreOrder::getPendingStatus();
    $this->status_approved = PreOrder::getApprovedStatus();
    $this->status_rejected = PreOrder::getRejectedStatus();
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
    // corrimiento de la fecha de apertura, por apertura manual
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
  public function closePeriod(PreOrderPeriodService $pps): void
  {
    // intento de cierre de un periodo con pre ordenes sin evaluar
    if ($pps->getPreOrdersPending($this->preorder_period)) {
      $this->dispatch('toast-event', event_data: [
        'event_type' => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'no puede cerrar este periodo hasta que evalue todas las pre ordenes respondidas'
      ]);

      return;
    }

    // corrimiento de la fecha de cierre, por cierre manual
    $this->preorder_period->period_end_at = Carbon::now()->format('Y-m-d');
    $this->preorder_period->save();

    Bus::chain([
      ClosePreOrderPeriodJob::dispatch($this->preorder_period),
      NotifySuppliersRequestForPreOrderClosedJob::dispatch($this->preorder_period),
    ]);

    // notificar del cierre a gerentes
    $gerentes_to_notify = User::role('gerente')->get();
    foreach ($gerentes_to_notify as $gerente) {
      SendEmailJob::dispatch($gerente->email, new ClosePreOrderPeriod($this->preorder_period));
    }
  }

  /**
   * abrir pdf en una nueva pestaña,
   * para poder visualizar y descargar.
   * @param int $id id de preorden base para el pdf
   * @return void
   */
  public function openPdfOrder($id): void
  {
    // denerar URL para ver el pdf
    $pdfUrl = route('open-pdf-order', ['id' => $id]);
    // disparar evento para abrir el PDF en nueva pestaña
    $this->dispatch('openPdfInNewTab', url: $pdfUrl);
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    $preorders = $this->preorder_period
      ->pre_orders()
      ->orderBy('id', 'desc')
      ->paginate(10);

    return view('livewire.suppliers.show-pre-order-period', compact('preorders'));
  }
}
