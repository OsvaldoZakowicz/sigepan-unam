<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use App\Models\Provision;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Arr;

//* componente donde asignar precios a los suministros del proveedor
// completa la tabla intermedia supplier_has_provisions
class AddToPriceList extends Component
{
  // proveedor
  public $supplier;

  // lista de suministros seleccionada
  public $provisions = [];

  //* montar datos
  public function mount($id)
  {
    $this->supplier = Supplier::findOrFail($id);
  }

  //* capturar evento de añadir provisiones a la lista
  #[On('add-provision')]
  public function onAddEvent($id)
  {
    // buscar y agregar a la lista
    $provision = Provision::findOrFail($id);
    $this->provisions = Arr::add($this->provisions, $provision->id, $provision);

  }

  public function render()
  {
    return view('livewire.suppliers.add-to-price-list');
  }
}
