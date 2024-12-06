<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use App\Models\Provision;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * *Componente 'alta de precios para el proveedor'
 * agrega precios a la lista del proveedor.
 */
class AddToPriceList extends Component
{
  // proveedor
  public $supplier;

  // lista de precios
  public Collection $prices;

  // reglas
  protected $rules = [
    'prices.*.provision_id'  =>  'required',
    'prices.*.price'         =>  ['required',  'numeric', 'regex:/^\d{1,6}(\.\d{1,2})?$/', 'min:1'],
  ];

  // mensajes
  protected $messages = [
    'prices.*.provision_id.required'  =>  'Debe elegir al menos un suministro',
    'prices.*.price.required'         =>  'El precio es obligatorio',
    'prices.*.price.numeric' => 'El precio es debe ser un número',
    'prices.*.price.regex' => 'El precio puede ser de hasta $999999.99',
    'prices.*.price.min' => 'El precio puede ser de minimo $1',
  ];

  /**
   * montar datos del componente
   * @param int $id id de un proveedor.
   * @return void
  */
  public function mount(int $id): void
  {
    $this->supplier = Supplier::findOrFail($id);
    $this->setPricesList();
  }

  /**
   * iniciar una coleccion de precios vacia
   * ['prices' => []]
   * @return void.
  */
  public function setPricesList(): void
  {
    $this->fill([
      'prices' => collect([]),
    ]);
  }

  /**
   * * agregar suministros a la lista de precios
   * provision_id, para mantener el id del suministro en el request.
   * * el evento proviene de SearchProvision::class
   * @param Provision $provision un suministro.
   * @return void.
  */
  #[On('add-provision')]
  public function addToPricesList(Provision $provision): void
  {

    foreach ($this->prices as $price) {
      if ($price['provision_id'] == $provision->id) {

        $this->dispatch('toast-event', toast_data: [
          'event_type' => 'info',
          'title_toast' => toastTitle('',true),
          'descr_toast' => 'ya existe en la lista de suministros!'
        ]);

        return;
      }
    }

    $this->prices->push([
      'provision'     =>  $provision,
      'provision_id'  =>  $provision->id,
      'price'         =>  '',
    ]);
  }

  /**
   * remover un suministro de la lista de precios
   * @param int $key clave del array de precios para el suministro.
  */
  public function removeFromPriceList(int $key): void
  {
    $this->prices->pull($key);
  }

  /**
   * guardar lista de precios
   * @return void.
  */
  public function save(): void
  {

    // si la lista esta vacia, retornar
    if ($this->prices->isEmpty()) {

      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'info',
        'title_toast' => toastTitle('',true),
        'descr_toast' => 'La lista de suministros esta vacia!'
      ]);

      return;
    }

    // validated_prices = [ ["provision_id" => 16, "price" => "123"], [], ... ]
    $validated_prices = $this->validate()['prices'];

    try {
      // guardar precios para los suministros del proveedor
      foreach ($validated_prices as $price) {
        $this->supplier->provisions()
          ->attach($price['provision_id'], ['price' => $price['price']]);
      }

      $this->setPricesList();

      session()->flash('operation-success', 'Los precios fueron creados correctamente');
      $this->redirectRoute('suppliers-suppliers-price-index', ['id' => $this->supplier->id], navigate: true);
    } catch (\Exception $e) {

      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'error',
        'title_toast' => toastTitle('fallida'),
        'descr_toast' => 'error: ' . $e->getMessage() . ', contacte al Administrador'
      ]);
    }
  }

  /**
   * renderizar vista
   * @return View
  */
  public function render(): View
  {
    return view('livewire.suppliers.add-to-price-list');
  }
}
