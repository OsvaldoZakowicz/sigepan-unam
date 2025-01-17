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

  // estados del periodo
  public bool $is_opened = false;
  public bool $is_scheduled = false;
  public bool $is_closed = false;

  /**
   * montar datos.
   * @param int $id id de un periodo de peticion de presupuestos.
   * @param QuotationPeriodService $quotation_period_service servicio.
   * @return void
  */
  public function mount(int $id, QuotationPeriodService $quotation_period_service): void
  {
    // periodo
    $this->period = RequestForQuotationPeriod::findOrFail($id);

    // estado: abierto
    if ($quotation_period_service->getStatusOpen() == $this->period->period_status_id) {
      $this->is_opened = true;
    }

    // estado: planificado
    if ($quotation_period_service->getStatusScheduled() == $this->period->period_status_id) {
      $this->is_scheduled = true;
    }

    // estado: cerrado
    if ($quotation_period_service->getStatusClosed() == $this->period->period_status_id) {
      $this->is_closed = true;
    }

  }

  public function test()
  {
    $this->period->period_status_id = 2;
    $this->period->save();

    $this->dispatch('test');
  }

  /**
   * abrir el periodo.
   * todo: como refrescar la vista para mostrar lo nuevo?
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
   * todo: como refrescar la vista para mostrar lo nuevo?
   * @return void
  */
  public function closePeriod(): void
  {
    CloseQuotationPeriodJob::dispatch($this->period);

    //$this->js("window.location.reload()");
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
