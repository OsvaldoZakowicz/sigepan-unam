<?php

namespace App\Livewire\Suppliers;

use App\Jobs\CloseQuotationPeriodJob;
use App\Models\RequestForQuotationPeriod;
use Livewire\Attributes\On;
use Livewire\Component;

class ShowBudgetPeriod extends Component
{
  public $period;
  public $period_provisions;
  public $period_quotations;

  public function mount($id)
  {
    $this->period = RequestForQuotationPeriod::findOrFail($id);
    $this->period_provisions = $this->period->provisions;
    $this->period_quotations = $this->period->quotations;
  }

  //* cerrar el periodo manualmente
  public function close()
  {
    // todo: verificar por que el dispatch no funciona
    /* CloseQuotationPeriodJob::dispatch($this->period); */

    $this->period->period_status_id = 3;
    $this->period->save();

    $this->redirectRoute('suppliers-budgets-periods-index');
  }

  public function render()
  {
    return view('livewire.suppliers.show-budget-period');
  }
}
