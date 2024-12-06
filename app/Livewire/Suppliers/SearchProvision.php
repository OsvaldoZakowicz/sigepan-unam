<?php

namespace App\Livewire\Suppliers;

use App\Models\Provision;
use App\Models\ProvisionTrademark;
use App\Models\ProvisionType;
use App\Models\Supplier;
use Illuminate\View\View;
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

  /**
   * montar datos del componente
   * @param int $supplier_id id de un proveedor.
   * @param bool $is_editing indica si se realiza una busqueda de edicion.
   * @return void
  */
  public function mount($supplier_id, $is_editing = false): void
  {
    $this->supplier = Supplier::findOrFail($supplier_id);
    $this->trademarks = ProvisionTrademark::orderBy('provision_trademark_name', 'asc')->get();
    $this->provision_types = ProvisionType::all();
    $this->is_editing = $is_editing;
  }

  /**
   * enviar un suministro de la lista de busqueda a la lista de precios
   * @param Provision $provision id del suministro.
   * @return void.
   */
  public function addProvision(Provision $provision): void
  {
    $this->dispatch('add-provision', provision: $provision);
  }

  /**
   * buscar suministros para el proveedor
   * en edicion: buscar asociados al proveedor.
  */
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

  /**
   * reiniciar la paginacion para buscar
   * @return void.
  */
  public function resetPagination(): void
  {
    $this->resetPage();
  }

  /**
   * renderizar vista
   * @return View
  */
  #[On('refresh-search')]
  public function render(): View
  {
    $provisions = $this->searchProvisions();
    return view('livewire.suppliers.search-provision', compact('provisions'));
  }
}
