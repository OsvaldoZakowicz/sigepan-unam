<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Component;

class PriceList extends Component
{
  use WithPagination;

  #[Url]
  public $search = '';

  // proveedor al que se le dara una lista de precios
  public $supplier;

  //* montar datos
  public function mount($id)
  {
    $this->supplier = Supplier::findOrFail($id);
  }

  //* buscar suministros con precio del proveedor
  // todo: filtrar por precio, marca, tipo
  public function searchSupplierProvisions()
  {
    return $this->supplier->provisions()
      ->when($this->search, function ($query) {
        $query->where('provision_name', 'like', '%' . $this->search . '%');
      })
      ->orderBy('id', 'desc')
      ->paginate(10);
  }

  // reiniciar paginacion al buscar
  public function resetPagination()
  {
    $this->resetPage();
  }

  public function render()
  {
    $provisions = $this->searchSupplierProvisions();

    return view('livewire.suppliers.price-list', compact('provisions'));
  }
}
