<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use Livewire\Component;

class ListSuppliers extends Component
{
  public function render()
  {
    $suppliers = Supplier::all();

    return view('livewire.suppliers.list-suppliers', compact('suppliers'));
  }
}
