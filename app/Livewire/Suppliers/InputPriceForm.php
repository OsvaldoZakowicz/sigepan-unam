<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use App\Models\Provision;
use App\Models\Supplier;
use Livewire\Attributes\On;

//* componente de formulario individual para actualizar un precio
class InputPriceForm extends Component
{
  public $supplier;
  public $provision;
  public $provision_array_key;

  // campos del formulario
  public $provision_price;

  public function mount($provision_id, $supplier_id, $provision_array_key)
  {
    $this->provision = Provision::findOrFail($provision_id);
    $this->supplier = Supplier::findOrFail($supplier_id);
    $this->provision_array_key = $provision_array_key;
  }

  //* al recibir notificacion de guardado
  // la notificacion proviene del componente livewire AddToPriceList
  #[On('save-prices')]
  public function savePrice()
  {

    // todo: validar precio

    // todo: como comunico errores de validacion?

    try {

      // todo: no duplicar suministros ya existentes con precios

      // guardar suministro con precio
      $this->supplier->provisions()->attach($this->provision->id, ['price' => $this->provision_price]);

      // todo: al guardar exitosamente, quitar suministro de la lista

    } catch (\Exception $e) {

      // todo: como manejo errores de cada form individual?

      // manejar errores
      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'error',
        'title_toast' => 'error',
        'descr_toast' => 'error: ' . $e->getMessage(),
      ]);

    }
  }

  //* al seleccionar un suministro para quitarlo de la lista
  // notifica al componente livewire AddToPriceList
  // $id del suministro a quitar, posicion del array
  public function removeProvision()
  {
    $this->dispatch('remove-provision', id: $this->provision_array_key);
  }

  public function render()
  {
    return view('livewire.suppliers.input-price-form');
  }
}
