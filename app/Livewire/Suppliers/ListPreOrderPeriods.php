<?php

namespace App\Livewire\Suppliers;

use App\Models\PreOrderPeriod;
use Livewire\WithPagination;
use Livewire\Component;

class ListPreOrderPeriods extends Component
{
  use WithPagination;

  public function mount()
  {

  }

  public function render()
  {
    $preorder_periods = PreOrderPeriod::orderBy('id', 'desc')->paginate(10);
    return view('livewire.suppliers.list-pre-order-periods', compact('preorder_periods'));
  }
}
