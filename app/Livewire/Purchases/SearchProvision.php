<?php

namespace App\Livewire\Purchases;

use App\Models\Supplier;
use App\Models\Provision;
use App\Models\Pack;
use App\Models\ProvisionTrademark;
use App\Models\ProvisionType;
use Illuminate\View\View;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Livewire\Component;

class SearchProvision extends Component
{
  use WithPagination;

  #[Url]
  public $search = '';

  #[Url]
  public $search_pack = '';

  #[Url]
  public $search_tr = '';

  #[Url]
  public $search_tr_pack = '';

  #[Url]
  public $search_ty = '';

  #[Url]
  public $search_ty_pack = '';

  #[Url]
  public $paginas = '5';

  // proveedor
  public $supplier;
  // marcas de suministros
  public $trademarks;
  // tipos de suministros
  public $provision_types;

  // toggle del objetivo de busqueda
  public $toggle = false;

  /**
   * boot de datos
   * @return void
   */
  public function boot(): void
  {
    $this->trademarks = ProvisionTrademark::orderBy('provision_trademark_name', 'asc')->get();
    $this->provision_types = ProvisionType::all();
  }

  /**
   * montar datos del componente
   * @param int $supplier_id id de un proveedor.
   * @param bool $is_editing indica si se realiza una busqueda de edicion.
   * @return void
   */
  #[On('refresh-search')]
  public function mount($supplier_id): void
  {
    $this->supplier = Supplier::findOrFail($supplier_id);
    $this->reset('toggle');
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
   * enviar un suministro de la lista de busqueda
   * @param Provision $provision
   * @return void
   */
  public function addProvision(Provision $provision): void
  {
    $this->dispatch('add-provision', provision: $provision);
  }

  /**
   * enviar un pack de la lista de busqueda
   * @param Pack $pack
   * @return void
   */
  public function addPack(Pack $pack): void
  {
    $this->dispatch('add-pack', pack: $pack);
  }

  /**
   * buscar suministros para el proveedor
   */
  public function searchProvisions()
  {
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

    return $result;
  }

  /**
   * buscar packs para el proveedor
   */
  public function searchPacks()
  {
    $result = $this->supplier->packs()
      ->has('provision') // incluir suministro del pack
      ->when($this->search_pack, function ($query) {
        $query->where('pack_name', 'like', '%' . $this->search_pack . '%');
      })
      ->when($this->search_tr_pack, function ($query) {
        $query->whereHas('provision', function ($q) {
          $q->where('provision_trademark_id', $this->search_tr_pack);
        });
      })
      ->when($this->search_ty_pack, function ($query) {
        $query->whereHas('provision', function ($q) {
          $q->where('provision_type_id', $this->search_ty_pack);
        });
      })
      ->orderBy('id', 'desc')
      ->paginate((int) $this->paginas);

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
   * limpiar filtros
   * @return void
   */
  public function clearFilters(): void
  {
    $this->reset([
      'search', 'search_tr', 'search_ty', 'search_pack', 'search_tr_pack', 'search_ty_pack', 'paginas'
    ]);
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    $provisions = $this->searchProvisions();
    $packs = $this->searchPacks();
    return view('livewire.purchases.search-provision', compact('provisions', 'packs'));
  }
}
