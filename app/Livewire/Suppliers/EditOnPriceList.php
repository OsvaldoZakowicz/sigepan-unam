<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use App\Models\Provision;
use App\Models\Pack;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * * componente 'editar precios para el proveedor'
 * edita los precios de la lista del proveedor.
 */
class EditOnPriceList extends Component
{
  public $supplier;

  public Collection $prices;

  // reglas
  protected $rules = [
    'prices.*.provision_id'  => 'nullable',
    'prices.*.pack_id'       => 'nullable',
    'prices.*.price'         => ['required',  'numeric', 'regex:/^\d{1,6}(\.\d{1,2})?$/', 'min:1'],
  ];

  // mensajes
  protected $messages = [
    'prices.*.price.required' => 'El precio es obligatorio',
    'prices.*.price.numeric'  => 'El precio es debe ser un número',
    'prices.*.price.regex'    => 'El precio puede ser de hasta $999999.99',
    'prices.*.price.min'      => 'El precio puede ser de minimo $1',
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
   * provision_id, para mantener el id del suministro en el request
   * pack_id, para mantener el id del pack en el request
   * * el evento proviene de SearchProvision::class
   * @param Provision $provision
   * @return void
  */
  #[On('add-provision')]
  public function addToPricesList(Provision $provision): void
  {

    foreach ($this->prices as $price) {
      if ($price['provision_id'] == $provision->id) {

        $this->dispatch('toast-event', toast_data: [
          'event_type' => 'info',
          'title_toast' => toastTitle('',true),
          'descr_toast' => 'el suministro ya existe en la lista de edicion!'
        ]);

        return;
      }
    }

    // obtener el precio
    $price = $provision->suppliers()
      ->wherePivot('supplier_id', $this->supplier->id)
      ->first()->pivot->price;

    $this->prices->push([
      'provision'     =>  $provision,
      'provision_id'  =>  $provision->id,
      'pack'          =>  null,
      'pack_id'       =>  null,
      'price'         =>  $price
    ]);
  }

  /**
   * * agregar packs a la lista de precios
   * provision_id, para mantener el id del suministro en el request
   * pack_id, para mantener el id del pack en el request
   * * el evento proviene de SearchProvision::class
   * @param Pack $pack
   * @return void
  */
  #[On('add-pack')]
  public function addPackToPriceList(Pack $pack): void
  {
    foreach ($this->prices as $price) {
      if ($price['pack_id'] == $pack->id) {

        $this->dispatch('toast-event', toast_data: [
          'event_type' => 'info',
          'title_toast' => toastTitle('',true),
          'descr_toast' => 'el pack ya existe en la lista de edicion!'
        ]);

        return;
      }
    }

    // obtener el precio
    $price = $pack->suppliers()
      ->wherePivot('supplier_id', $this->supplier->id)
      ->first()->pivot->price;

    $this->prices->push([
      'provision'     =>  null,
      'provision_id'  =>  null,
      'pack'          =>  $pack,
      'pack_id'       =>  $pack->id,
      'price'         =>  $price
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

    $validated_prices = $this->validate()['prices'];

    //dd($validated_prices);

    try {

      // editar precios para los suministros y packs del proveedor
      foreach ($validated_prices as $price) {

        if ($price['provision_id'] !== null) {

          // es un suministro
          $this->supplier->provisions()
            ->updateExistingPivot($price['provision_id'], ['price' => $price['price']]);

        } else {

          // es un pack
          $this->supplier->packs()
            ->updateExistingPivot($price['pack_id'], ['price' => $price['price']]);

        }

      }

      $this->setPricesList();

      session()->flash('operation-success', 'Los precios fueron editados correctamente');
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
    return view('livewire.suppliers.edit-on-price-list');
  }
}
