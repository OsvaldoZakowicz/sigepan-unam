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
  // de aqui obtengo suministros y packs de interes
  public array | null $quotations_ranking = null;

  // datos de pre ordenes para el periodo, cuando no depende de un ranking de presupuestos
  // de aqui obtengo suministros y packs de interes
  public array | null $provisions_and_packs = null;

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

  // suministros y packs no cubiertos
  public $uncovered_provisions = null;
  public $uncovered_packs = null;
  public bool $has_uncovered_items = false;

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
  public function mount(QuotationPeriodService $qps, PreOrderPeriodService $pps, int $id): void
  {
    $this->preorder_period = PreOrderPeriod::findOrFail($id);
    $this->period_status = $this->preorder_period->status->id;

    /**
     * necesito crear nuevamente datos de ranking de presupuestos,
     * para obtener suministros y packs de interes. Siempre que
     * el periodo de pre orden parta de un periodo presupuestario
     */
    if ($this->preorder_period->quotation_period_id !== null) {

      // generar ranking inicial del periodo presupuestario
      // para obtener suministros y packs de interes
      $this->quotations_ranking = $qps->comparePricesBetweenQuotations($this->preorder_period->quotation_period_id);
    } else {

      // generar array de datos con suministros y packs de interes
      $this->provisions_and_packs = $pps->getProvisionAndPacksData($this->preorder_period->id);
    }

    /**
     * para periodos cerrados:
     * 1- necesito obtener suministros y packs no cubiertos en las pre ordenes respondidas.
     * 2- usarlos para obtener por cada uno de ellos proveedores alternativos, del ranking base o desde la base de datos.
     */
    if ($this->period_status === $this->closed) {
      $uncovered_items = $pps->getUncoveredItems($this->preorder_period);
      $items_with_suppliers = $pps->getAlternativeSuppliersForUncoveredItems($uncovered_items, $this->quotations_ranking);

      $this->uncovered_provisions = $items_with_suppliers['uncovered_provisions_with_alternative_suppliers']?->toArray() ?? [];
      $this->uncovered_packs = $items_with_suppliers['uncovered_packs_with_alternative_suppliers']?->toArray() ?? [];

      $this->has_uncovered_items = !empty($this->uncovered_provisions) || !empty($this->uncovered_packs);
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

      $this->dispatch('toast-event', toast_data: [
        'event_type'  => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'No se puede cerrar el periodo, existen pre ordenes respondidas por proveedores que debe evaluar. Para evaluar, acceda a "ver preorden" y rechaze o apruebe la misma.',
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
