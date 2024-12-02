<?php

namespace App\Livewire\Suppliers;

use App\Models\Quotation;
use Livewire\Component;

class ShowBudgetResponse extends Component
{
  public $quotation;

  public function mount($id)
  {
    $this->quotation = Quotation::findOrFail($id);
  }

  public function render()
  {
    return view('livewire.suppliers.show-budget-response');
  }
}
