<?php

namespace App\Livewire\Suppliers;

use App\Models\Provision;
use App\Models\ProvisionTrademark;
use App\Models\ProvisionType;
use App\Models\Supplier;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Component;

class SearchProvision extends Component
{
  use WithPagination;

  #[Url]
  public $search = '';
  #[Url]
  public $search_tr = '';
  #[Url]
  public $search_ty = '';
  #[Url]
  public $paginas = '5';

  // proveedor
  public $supplier;
  // marcas de suministros
  public $trademarks;
  // tipos de suministros
  public $provision_types;

  // busqueda de edicion
  public $is_editing;

  // montar datos
  public function mount($supplier_id, $is_editing = false)
  {
    $this->supplier = Supplier::findOrFail($supplier_id);
    $this->trademarks = ProvisionTrademark::orderBy('provision_trademark_name', 'asc')->get();
    $this->provision_types = ProvisionType::all();
    $this->is_editing = $is_editing;
  }

  // reiniciar la paginacion al buscar
  public function resetPagination()
  {
    $this->resetPage();
  }

  //* enviar la provision elegida mediante un evento
  // notifica al componente livewire AddToPriceList
  public function addProvision($id)
  {
    $this->dispatch('append-provision', id: $id);
  }

  // * buscar suministros para el proveedor
  // en alta o edicion
  public function searchProvisions()
  {
    if ($this->is_editing) {
      // verdadero que estoy editando
      //* buscar suministros con precios del proveedor
      $result = $this->supplier->provisions()
        ->when($this->search, function ($query) {
          $query->where('provision_name', 'like', '%' . $this->search . '%');
        })
        ->when($this->search_tr, function ($query) {
          $query->where('provision_trademark_id', $this->search_tr);
        })
        ->when($this->search_ty, function ($query) {
          $query->where('provision_type_id', $this->search_ty);
        })
        ->orderBy('id', 'desc')
        ->paginate((int) $this->paginas);

    } else {
      //* buscar suministros NO asociados al proveedor
      $result = Provision::whereDoesntHave('suppliers', function ($query) {
          $query->where('supplier_id', $this->supplier->id);
        })
        ->when($this->search, function ($query) {
          $query->where('provision_name', 'like', '%' . $this->search . '%');
        })
        ->when($this->search_tr, function ($query) {
          $query->where('provision_trademark_id', $this->search_tr);
        })
        ->when($this->search_ty, function ($query) {
          $query->where('provision_type_id', $this->search_ty);
        })
        ->orderBy('id', 'desc')
        ->paginate((int) $this->paginas);
    }

    return $result;
  }

  #[On('refresh-search')]
  public function render()
  {
    $provisions = $this->searchProvisions();

    return view('livewire.suppliers.search-provision', compact('provisions'));
  }
}
