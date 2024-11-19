<?php

namespace App\Livewire\Suppliers;

use App\Models\Provision;
use App\Models\ProvisionTrademark;
use App\Models\ProvisionType;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Component;

class AllPricesList extends Component
{
  use WithPagination;

  #[Url]
  public $search = '';
  #[Url]
  public $trademark_filter = '';
  #[Url]
  public $type_filter = '';

  public $trademarks;
  public $provision_types;

  public function mount()
  {
    $this->trademarks = ProvisionTrademark::all();
    $this->provision_types = ProvisionType::all();
  }

  //* buscar suministros con sus proveedores
  public function searchProvisionsSuppliers()
  {
    $result = Provision::with('suppliers')
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
      ->paginate(8);

    return $result;
  }

  // reiniciar paginacion al buscar
  public function resetPagination()
  {
    $this->resetPage();
  }

  public function render()
  {
    $all_provisions = $this->searchProvisionsSuppliers();

    return view('livewire.suppliers.all-prices-list', compact('all_provisions'));
  }
}
