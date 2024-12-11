<?php

namespace App\Livewire\Suppliers;

use App\Jobs\CloseQuotationPeriodJob;
use App\Models\RequestForQuotationPeriod;
use App\Services\Supplier\QuotationPeriodService;
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
  public bool $is_opened = false;
  public bool $is_scheduled = false;

  /**
   * montar datos
   * @param int $id id de un periodo de peticion de presupuestos
   * @return void
  */
  public function mount(int $id, QuotationPeriodService $quotation_period_service): void
  {
    // periodo
    $this->period = RequestForQuotationPeriod::findOrFail($id);

    // estado
    $opened_status = $quotation_period_service->getStatusOpen();
    $scheduled_status = $quotation_period_service->getStatusScheduled();

    if ($opened_status == $this->period->period_status_id) {
      $this->is_opened = true;
    }

    if ($scheduled_status == $this->period->period_status_id) {
      $this->is_scheduled = true;
    }
  }

  /**
   * abrir el periodo manualmente
   * @return void
  */
  public function openPeriod(): void
  {
    // todo
  }

  /**
   * cerrar el periodo manualmente
   * @return void
  */
  public function closePeriod(): void
  {
    // todo: verificar por que el dispatch no funciona
    /* CloseQuotationPeriodJob::dispatch($this->period); */

    $this->period->period_status_id = 3;
    $this->period->save();

    // todo: no redireccionar, refrescar vista y mostrar cierre

    $this->redirectRoute('suppliers-budgets-periods-index');
  }

  /**
   * renderizar vista
   * NOTA: las variables con paginacion deben enviarse a la vista mediante compact()
   * @return View
  */
  public function render(): View
  {
    $period_provisions = $this->period->provisions()->paginate(5);
    $period_quotations = $this->period->quotations()->paginate(5);

    return view('livewire.suppliers.show-budget-period', compact('period_provisions', 'period_quotations'));
  }
}
