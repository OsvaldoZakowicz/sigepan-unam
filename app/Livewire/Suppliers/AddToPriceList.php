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

  // montar datos
  public function mount($id)
  {
    $this->supplier = Supplier::findOrFail($id);
    $this->provision_array_key = 0;
  }

  //* funcion para vaciar todo el array de suministros elegidos
  // desde el boton vaciar lista
  public function refresh()
  {
    $this->provisions = [];
  }

  //* capturar evento de añadir suministros a la lista
  // carga el suministro en una lista donde cada suministro espera recibir un precio
  #[On('append-provision')]
  public function onAppendEvent($id)
  {
    // buscar suministro
    $provision = Provision::findOrFail($id);

    // no agregar a la lista si existe
    if (in_array($provision->id, $this->provisions)) {

      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'Este suministro ya está en la lista!'
      ]);

      return;
    }

    // agregar a la lista
    // el array provisions es: [ 'provision_array_key' => 'provision_id' ]
    $this->provisions = Arr::add($this->provisions, $this->provision_array_key, $provision->id);
    $this->provision_array_key++;

  }

  //* capturar evento de remover suministro de la lista
  // quita el suministro de la lista, recibe el id de array de suministros
  #[On('remove-provision')]
  public function onRemoveEvent($id)
  {
    // uso unset y afecto directamente al array de suministros
    unset($this->provisions[$id]);
  }

  //* enviar evento guardar precios y refrescar busqueda
  // notificar a cada componente livewire InputPriceForm
  public function save()
  {
    //no hacer nada si la lista de suministros esta vacia
    if (count($this->provisions) === 0) {
      return;
    }

    // guardar al menos uno
    $this->dispatch('save-prices')->to(InputPriceForm::class);

    // refrescar cuadro de busqueda
    $this->dispatch('refresh-search')->to(SearchProvision::class);
  }

  //* funcion para notificar de operacion de alta de precios exitosa
  // especificamente remover del array los suministros guardados,
  // cada evento disparado verifica que el array este vacio, si lo esta, mostrara un mensaje de exito
  #[On('save-success')]
  public function onSaveSuccessEvent($id)
  {
    // uso unset y afecto directamente al array de suministros
    unset($this->provisions[$id]);

    // todo: buscar una mejor forma de notificar al final
    // no contempla quitar suministros de la lista y que sea el ultimo
    if (is_array($this->provisions) && empty($this->provisions)) {

      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'success',
        'title_toast' => toastTitle('exitosa'),
        'descr_toast' => 'Todos los precios fueron guardados con éxito!'
      ]);

    }
  }

  public function render()
  {
    return view('livewire.suppliers.add-to-price-list');
  }
}
