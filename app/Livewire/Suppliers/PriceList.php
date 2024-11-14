<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use App\Models\Provision;
use Illuminate\Support\Arr;
use Livewire\Attributes\On;
use Livewire\Component;

class PriceList extends Component
{
  // proveedor al que se le dara una lista de precios
  public $supplier;

  //* montar datos
  public function mount($id)
  {
    $this->supplier = Supplier::findOrFail($id);
  }

  public function render()
  {
    return view('livewire.suppliers.price-list');
  }
}
