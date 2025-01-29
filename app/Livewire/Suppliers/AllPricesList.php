<?php

namespace App\Livewire\Suppliers;

use App\Models\Pack;
use App\Models\Provision;
use App\Models\ProvisionTrademark;
use App\Models\ProvisionType;
use Illuminate\View\View;
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

  // alternar busqueda entre suministros y packs
  public $toggle;

  /**
   * boot de datos
   * @return void
  */
  public function boot(): void
  {
    $this->trademarks = ProvisionTrademark::all();
    $this->provision_types = ProvisionType::all();
  }

  /**
   * montar datos
   * @return void
  */
  public function mount(): void
  {
    $this->toggle = false;
  }

  /**
   * cambiar busqueda
   * alternar entre busqueda de suministros individuales o packs
   * @return void
  */
  public function toggleSearch(): void
  {
    $this->toggle = !$this->toggle;
  }

  /**
   * buscar suministros con sus proveedores
   * @return mixed
  */
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

  /**
   * buscar packs con sus proveedores
   * @return mixed
  */
  public function searchPackSuppliers()
  {
    $result = Pack::with('suppliers')
      ->has('provision')
      ->when($this->search, function ($query) {
        $query->where('pack_name', 'like', '%' . $this->search . '%');
      })
      ->when($this->trademark_filter, function ($query) {
        $query->whereHas('provision', function ($q) {
          $q->where('provision_trademark_id', $this->trademark_filter);
        });
      })
      ->when($this->type_filter, function ($query) {
        $query->whereHas('provision', function ($q) {
          $q->where('provision_type_id', $this->type_filter);
        });
      })
      ->orderBy('id', 'desc')
      ->paginate(8);

    return $result;
  }

  /**
   * reiniciar paginacion al buscar
   * @return void
  */
  public function resetPagination(): void
  {
    $this->resetPage();
  }

  /**
   * renderizar vista
   * @return view
  */
  public function render(): View
  {
    $all_provisions = $this->searchProvisionsSuppliers();
    $all_packs = $this->searchPackSuppliers();
    return view('livewire.suppliers.all-prices-list', compact('all_provisions', 'all_packs'));
  }
}
