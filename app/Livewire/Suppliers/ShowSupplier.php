<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use App\Models\Supplier;

class ShowSupplier extends Component
{
  public $supplier;

  public function mount($id)
  {
    $this->supplier = Supplier::withTrashed()
      ->with(['user' => function ($query) {
        $query->withTrashed();
      }, 'address' => function ($query) {
        $query->withTrashed();
      }])
      ->findOrFail($id);
  }

  public function render()
  {
    return view('livewire.suppliers.show-supplier');
  }
}
