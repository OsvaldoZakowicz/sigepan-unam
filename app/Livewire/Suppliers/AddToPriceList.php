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
  // key autoincremental para el array de suministros seleccionados
  public $provision_key;

  //* montar datos
  public function mount($id)
  {
    $this->supplier = Supplier::findOrFail($id);
    $this->provision_key = 0;
  }

  //* capturar evento de añadir provisiones a la lista
  #[On('add-provision')]
  public function onAddEvent($id)
  {
    // buscar suministro
    $provision = Provision::findOrFail($id);

    // no agregar a la lista si existe
    if (in_array($provision->id, $this->provisions)) {
      return;
    }

    // agregar a la lista
    // lista [ 'id' => 'provision_id' ]
    $this->provisions = Arr::add($this->provisions, $this->provision_key, $provision->id);
    $this->provision_key++;

  }

  //* guardar precios
  public function save()
  {
    $this->dispatch('save-prices');
  }

  public function render()
  {
    return view('livewire.suppliers.add-to-price-list');
  }
}
