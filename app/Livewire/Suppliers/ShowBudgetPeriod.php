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

  // calculo de comparativa de precios
  public array $prices_between_quotations;

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
    // periodo
    $this->period = RequestForQuotationPeriod::findOrFail($id);

    // comparacion de precios de suministros entre presupuestos
    $this->prices_between_quotations = $qps->comparePricesBetweenQuotations($this->period->id);
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
    $period_provisions = $this->period->provisions()->paginate(5);
    $period_quotations = $this->period->quotations()->paginate(5);

    return view('livewire.suppliers.show-budget-period', compact('period_provisions', 'period_quotations'));
  }
}
