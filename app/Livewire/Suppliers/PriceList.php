<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use App\Models\ProvisionTrademark;
use App\Models\ProvisionType;
use Illuminate\View\View;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Component;

class PriceList extends Component
{
  use WithPagination;

  #[Url]
  public $search = '';

  #[Url]
  public $trademark_filter = '';

  #[Url]
  public $type_filter = '';

  // proveedor al que se le dara una lista de precios
  public $supplier;
  // marcas
  public $trademarks;
  // tipos de suministros
  public $provision_types;

  /**
   * montar datos del componente
   * @param int $id id de un proveedor.
   * @return void
  */
  public function mount(int $id): void
  {
    $this->supplier = Supplier::findOrFail($id);
    $this->trademarks = ProvisionTrademark::orderBy('id', 'desc')->get();
    $this->provision_types = ProvisionType::all();
  }

  /**
   * buscar suministos del proveedor
   * @return mixed
  */
  public function searchSupplierProvisions()
  {
    return $this->supplier->provisions()
      ->when($this->search, function ($query) {
        $query->where('provision_name', 'like', '%' . $this->search . '%');
      })
      ->when($this->trademark_filter, function ($query) {
        $query->where('provision_trademark_id', $this->trademark_filter);
      })
      ->when($this->type_filter, function ($query) {
        $query->where('provision_type_id', $this->type_filter);
      })
      ->orderBy('id', 'desc')
      ->paginate(10);
  }

  /**
   * reiniciar la paginacion para buscar
   * @return void.
  */
  public function resetPagination()
  {
    $this->resetPage();
  }

  /**
   * eliminar precio del suministro
   * quita la asociacion suministro con proveedor y el precio,
   * no borra el suministro.
   * @param int $id del suministro
   * @return void
  */
  public function delete(int $id): void
  {
    try {

      $this->supplier->provisions()
        ->where('provision_id', $id)->first()->pivot->delete();

      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'success',
        'title_toast' =>  toastTitle('exitosa'),
        'descr_toast' =>  toastSuccessBody('precio del suministro', 'eliminado'),
      ]);

    } catch (\Exception $e) {

      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'error',
        'title_toast' =>  toastTitle('fallida'),
        'descr_toast' =>  'error: ' . $e->getMessage() . ' contacte al Administrador',
      ]);

    }
  }

  /**
   * renderizar vista
   * @return View
  */
  public function render(): View
  {
    $provisions_with_price = $this->searchSupplierProvisions();
    return view('livewire.suppliers.price-list', compact('provisions_with_price'));
  }
}
