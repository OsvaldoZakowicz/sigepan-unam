<?php

namespace App\Livewire\Suppliers;

use App\Models\Provision;
use App\Models\Supplier;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Component;

class SearchProvision extends Component
{
  use WithPagination;

  #[Url]
  public $search = '';

  // proveedor
  public $supplier_id;

  // montar datos
  public function mount($supplier_id)
  {
    $this->supplier_id = $supplier_id;
  }

  //* enviar la provision elegida mediante un evento
  // notifica al componente livewire AddToPriceList
  public function addProvision($id)
  {
    $this->dispatch('append-provision', id: $id);
  }

  public function render()
  {
    $provisions = Provision::when($this->search, function ($query) {
      $query->where('provision_name', 'like', '%' . $this->search . '%');
    })->orderBy('id', 'desc')->paginate(4);

    $provisions_filter = Provision::whereDoesntHave('suppliers', function ($query) {
      $query->where('supplier_id', $this->supplier_id);
    })->paginate(4);

    return view('livewire.suppliers.search-provision', compact('provisions_filter'));
  }
}
