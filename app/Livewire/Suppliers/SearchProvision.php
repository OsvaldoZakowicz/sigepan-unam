<?php

namespace App\Livewire\Suppliers;

use App\Models\Provision;
use App\Models\ProvisionTrademark;
use App\Models\ProvisionType;
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

  // proveedor
  public $supplier_id;
  // marcas de suministros
  public $trademarks;
  // tipos de suministros
  public $provision_types;

  // montar datos
  public function mount($supplier_id)
  {
    $this->supplier_id = $supplier_id;
    $this->trademarks = ProvisionTrademark::all();
    $this->provision_types = ProvisionType::all();
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

  //* buscar suministros NO asociados al proveedor
  // los mismos deben asociarse con un precio al proveedor
  public function searchProvisions()
  {
    return Provision::whereDoesntHave('suppliers', function ($query) {
                        $query->where('supplier_id', $this->supplier_id);
                      })->when($this->search, function ($query) {
                        $query->where('provision_name', 'like', '%' . $this->search . '%');
                      })->when($this->search_tr, function ($query) {
                        $query->where('provision_trademark_id', $this->search_tr);
                      })->when($this->search_ty, function ($query) {
                        $query->where('provision_type_id', $this->search_ty);
                      })->orderBy('id', 'desc')
                        ->paginate(4);
  }

  public function render()
  {
    $provisions = $this->searchProvisions();

    return view('livewire.suppliers.search-provision', compact('provisions'));
  }
}
