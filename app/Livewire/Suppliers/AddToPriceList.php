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
  public $provision_array_key;

  //* montar datos
  public function mount($id)
  {
    $this->supplier = Supplier::findOrFail($id);
    $this->provision_array_key = 0;
  }

  //* capturar evento de añadir provision a la lista
  // carga la provision en una lista donde cada provision espera recibir un precio
  #[On('append-provision')]
  public function onAppendEvent($id)
  {
    // buscar suministro
    $provision = Provision::findOrFail($id);

    // no agregar a la lista si existe
    if (in_array($provision->id, $this->provisions)) {

      // todo: mensaje "ya esta en la lista"
      return;
    }

    // agregar a la lista
    // array provisions es: [ 'provision_array_key' => 'provision_id' ]
    $this->provisions = Arr::add($this->provisions, $this->provision_array_key, $provision->id);
    $this->provision_array_key++;

  }

  //* capturar evento de remover provision de la lista
  // quita la provision de la lista, recibe el id de array de la provision
  #[On('remove-provision')]
  public function onRemoveEvent($id)
  {
    // uso unset y afecto directamente al array de suministros
    unset($this->provisions[$id]);
  }

  //* guardar precios
  // notificar a cada componente livewire InputPriceForm
  public function save()
  {
    $this->dispatch('save-prices')->to(InputPriceForm::class);
  }

  public function render()
  {
    return view('livewire.suppliers.add-to-price-list');
  }
}
