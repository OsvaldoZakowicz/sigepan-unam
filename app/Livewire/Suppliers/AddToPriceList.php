<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use App\Models\Provision;
use App\Models\Pack;
use App\Models\RequestForQuotationPeriod;
use App\Models\PreOrderPeriod;
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
  // provision_id y pack_id diferencian de quien es el precio
  protected $rules = [
    'prices.*.provision_id'  =>  'nullable',
    'prices.*.pack_id'       =>  'nullable',
    'prices.*.price'         =>  ['required',  'numeric', 'regex:/^\d{1,6}(\.\d{1,2})?$/', 'min:1'],
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

    // no agregar si existen periodos de presupuesto o preorden activos
    if (RequestForQuotationPeriod::whereHas('status', function ($query) {
      $query->whereIn('status_code', [0, 1]); // programado, abierto
    })->count() > 0) {
      
      session()->flash('operation-info', 'No puede agregar precios mientras existan periodos de presupuestos programados o abiertos');
      $this->redirectRoute('suppliers-suppliers-price-index', $this->supplier->id);
    }

    if (PreOrderPeriod::whereHas('status', function ($query) {
      $query->whereIn('status_code', [0, 1]); // programado, abierto
    })->count() > 0) {
      
      session()->flash('operation-info', 'No puede agregar precios mientras existan periodos de preordenes programados o abiertos');
      $this->redirectRoute('suppliers-suppliers-price-index', $this->supplier->id);
    }

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
   * @param Provision $provision un suministro
   * @return void
  */
  #[On('add-provision')]
  public function addProvisionToPricesList(Provision $provision): void
  {

    foreach ($this->prices as $price) {
      if ($price['provision_id'] == $provision->id) {

        $this->dispatch('toast-event', toast_data: [
          'event_type' => 'info',
          'title_toast' => toastTitle('',true),
          'descr_toast' => 'el suministro ya existe en la lista de alta!'
        ]);

        return;
      }
    }

    $this->prices->push([
      'provision'     =>  $provision,
      'provision_id'  =>  $provision->id,
      'pack'          =>  null,
      'pack_id'       =>  null,
      'price'         =>  '',
    ]);
  }

  /**
   * * agregar packs a la lista de precios
   * pack_id, para mantener el id del pack en el request
   * * el evento proviene de SearchProvision::class
   * @param Pack $pack un pack
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
          'descr_toast' => 'el pack ya existe en la lista de alta!'
        ]);

        return;
      }
    }

    $this->prices->push([
      'provision'     =>  null,
      'provision_id'  =>  null,
      'pack'          =>  $pack,
      'pack_id'       =>  $pack->id,
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

    $validated_prices = $this->validate()['prices'];

    try {

      // guardar precios para los suministros y packs del proveedor
      foreach ($validated_prices as $price) {

        if ($price['provision_id'] !== null) {

          // es un suministro
          $this->supplier->provisions()
            ->attach($price['provision_id'], ['price' => $price['price']]);

        } else {

          // es un pack
          $this->supplier->packs()
            ->attach($price['pack_id'], ['price' => $price['price']]);

        }

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
