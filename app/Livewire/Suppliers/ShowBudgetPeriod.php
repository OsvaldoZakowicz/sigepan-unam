<?php

namespace App\Livewire\Suppliers;

use App\Jobs\CloseQuotationPeriodJob;
use App\Jobs\OpenQuotationPeriodJob;
use App\Models\RequestForQuotationPeriod;
use App\Services\Supplier\QuotationPeriodService;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

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
    OpenQuotationPeriodJob::dispatch($this->period);
  }

  /**
   * cerrar el periodo.
   * @return void
  */
  public function closePeriod(): void
  {
    CloseQuotationPeriodJob::dispatch($this->period);
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
