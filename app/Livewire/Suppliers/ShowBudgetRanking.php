<?php

namespace App\Livewire\Suppliers;

use App\Models\RequestForQuotationPeriod;
use Livewire\Component;

class ShowBudgetRanking extends Component
{
  public RequestForQuotationPeriod $period;

  public function mount(int $id)
  {
    $this->period = RequestForQuotationPeriod::findOrFail($id);
  }

  public function render()
  {
    return view('livewire.suppliers.show-budget-ranking');
  }
}
