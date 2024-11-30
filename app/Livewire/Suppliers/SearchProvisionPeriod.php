<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use App\Services\Supplier\SupplierService;
use App\Models\Provision;

class SearchProvisionPeriod extends Component
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

  // periodo (al editar)
  public $period;

  // marcas de suministros
  public $trademarks;
  // tipos de suministros
  public $provision_types;

  // busqueda de edicion
  public $is_editing;

  public function mount(SupplierService $sps, $is_editing = false)
  {
    $this->trademarks = $sps->getProvisionTrademarks();
    $this->provision_types = $sps->getProvisionTypes();
    $this->is_editing = $is_editing;
  }

  // reiniciar la paginacion al buscar
  public function resetPagination()
  {
    $this->resetPage();
  }

  //* enviar la provision elegida mediante un evento
  // notifica al componente livewire CreateBudgetPeriod
  public function addProvision($id)
  {
    $this->dispatch('append-provision', id: $id)->to(CreateBudgetPeriod::class);
  }

  // * buscar suministros para el periodo de peticion
  // en alta o edicion
  public function searchProvisions()
  {
    if ($this->is_editing) {
      // verdadero que estoy editando
      //* buscar suministros con precios del proveedor
      /* $result = $this->supplier->provisions()
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
        ->paginate((int) $this->paginas); */

    } else {
      //* buscar todos los suministros con proveedor
      $result = Provision::whereHas('suppliers', function ($query) {
          $query->where('status_is_active', true);
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

    return view('livewire.suppliers.search-provision-period', compact('provisions'));
  }
}
