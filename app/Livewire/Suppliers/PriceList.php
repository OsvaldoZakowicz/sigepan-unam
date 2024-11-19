<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use App\Models\ProvisionTrademark;
use App\Models\ProvisionType;
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

  //* montar datos
  public function mount($id)
  {
    $this->supplier = Supplier::findOrFail($id);
    $this->trademarks = ProvisionTrademark::orderBy('id', 'desc')->get();
    $this->provision_types = ProvisionType::all();
  }

  //* buscar suministros con precio del proveedor
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

  // reiniciar paginacion al buscar
  public function resetPagination()
  {
    $this->resetPage();
  }

  //* eliminar precio del suministro
  // quita la asociacion suministro con proveedor y el precio,
  // no borra el suministro.
  public function delete($id)
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

  public function render()
  {
    $provisions_with_price = $this->searchSupplierProvisions();

    return view('livewire.suppliers.price-list', compact('provisions_with_price'));
  }
}
