<?php

namespace App\Livewire\Suppliers;

use App\Models\User;
use Livewire\Component;
use App\Models\Quotation;
use Illuminate\View\View;
use App\Jobs\SendEmailJob;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use App\Mail\CloseQuotationPeriod;
use Illuminate\Support\Facades\Bus;
use App\Jobs\OpenQuotationPeriodJob;
use App\Jobs\CloseQuotationPeriodJob;
use App\Jobs\UpdateSuppliersPricesJob;
use App\Models\RequestForQuotationPeriod;
use App\Services\Supplier\QuotationPeriodService;
use App\Jobs\NotifySuppliersRequestForQuotationClosedJob;
use App\Jobs\NotifySuppliersRequestForQuotationReceivedJob;

/**
 * mostrar un periodo presupuestario
 * todo: mostrar los provedores a los que se contactaran
 */
class ShowBudgetPeriod extends Component
{
  use WithPagination;

  public RequestForQuotationPeriod $period;

  // posibles estados del periodo
  public int $scheduled;
  public int $opened;
  public int $closed;

  // estado del periodo
  public int $period_status;

  // conteo de presupuestos respondidos
  public int $count_quotations;

  /**
   * boot de constantes
   * @param $qps quotation period service
   * @return void
   */
  public function boot(QuotationPeriodService $qps): void
  {
    // ids de estados
    $this->scheduled = $qps->getStatusScheduled();
    $this->opened = $qps->getStatusOpen();
    $this->closed = $qps->getStatusClosed();
  }

  /**
   * montar datos.
   * @param int $id id de un periodo de peticion de presupuestos.
   * @param QuotationPeriodService $quotation_period_service servicio.
   * @return void
   */
  public function mount(QuotationPeriodService $qps, int $id): void
  {
    $this->period = RequestForQuotationPeriod::findOrFail($id);
    $this->period_status = $this->period->period_status_id;
    $this->count_quotations = $qps->countQuotationsFromPeriod($this->period->id);
  }

  /**
   * abrir el periodo.
   * @return void
   */
  public function openPeriod(): void
  {
    $this->period->period_start_at = Carbon::now()->format('Y-m-d');
    $this->period->save();

    Bus::chain([
      OpenQuotationPeriodJob::dispatch($this->period),
      NotifySuppliersRequestForQuotationReceivedJob::dispatch($this->period),
    ]);
  }

  /**
   * cerrar el periodo.
   * @return void
   */
  public function closePeriod(): void
  {
    $this->period->period_end_at = Carbon::now()->format('Y-m-d');
    $this->period->save();

    Bus::chain([
      CloseQuotationPeriodJob::dispatch($this->period),
      UpdateSuppliersPricesJob::dispatch($this->period),
      NotifySuppliersRequestForQuotationClosedJob::dispatch($this->period),
    ]);

    $gerentes_to_notify = User::role('gerente')->get();
    foreach ($gerentes_to_notify as $gerente) {
      SendEmailJob::dispatch($gerente->email, new CloseQuotationPeriod($this->period));
    };
  }

  /**
   * abrir pdf de presupuesto
   * @param int $id de presupuesto
   */
  public function openPdf(int $id): void
  {
    if (!$this->hasSomeStock($id)) {
      return;
    }

    // generar URL para ver el pdf
    $pdfUrl = route('open-pdf-quotation-supplier', ['id' => $id]);
    // disparar evento para abrir el PDF en nueva pestaña
    $this->dispatch('openPdfInNewTab', url: $pdfUrl);
  }

  /**
   * comprobar si el presupuesto tiene al menos un item en stock
   * @return bool
   */
  public function hasSomeStock(int $id): bool
  {
    $quotation = Quotation::find($id);
    $cant_provisions = $quotation->provisions()->where('has_stock', true)->count();
    $cant_packs = $quotation->packs()->where('has_stock', true)->count();

    return (($cant_provisions + $cant_packs) > 0) ? true : false;
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    // suministros y packs de interes para el periodo
    $period_provisions = $this->period->provisions()->paginate(5);
    $period_packs = $this->period->packs()->paginate(5);

    // presupuestos del periodo
    $period_quotations = $this->period->quotations()->paginate(5);

    return view('livewire.suppliers.show-budget-period', compact('period_provisions', 'period_packs', 'period_quotations'));
  }
}
