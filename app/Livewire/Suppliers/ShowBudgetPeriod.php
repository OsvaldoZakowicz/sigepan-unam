<?php

namespace App\Livewire\Suppliers;

use App\Models\RequestForQuotationPeriod;
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

  public function render()
  {
    return view('livewire.suppliers.show-budget-period');
  }
}
