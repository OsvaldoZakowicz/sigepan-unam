<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use App\Services\Supplier\SupplierService;
use App\Models\Provision;
use App\Models\Pack;

/**
 * * Componente de busqueda de suministros y packs para periodos
 * presupuestarios o de pre orden.
 * - se usa en la creacion de periodo presupuestario
 * - se usa en la creacion de periodo de pre ordenes
 */
class SearchProvisionPeriod extends Component
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

  // marcas de suministros
  public $trademarks;

  // tipos de suministros
  public $provision_types;

  // toggle del objetivo de busqueda
  public $toggle;

  /**
   * boot de datos
   * @return void
   */
  public function boot(SupplierService $sps): void
  {
    $this->trademarks = $sps->getProvisionTrademarks();
    $this->provision_types = $sps->getProvisionTypes();
  }

  /**
   * montar datos
   * @return void
   */
  public function mount()
  {
    $this->toggle = false;
  }

  /**
   * cambiar busqueda
   * alternar entre busqueda de suministros individuales o packs
   * al hacer toggle limpiar parametros de busqueda y pagina
   * @return void
   */
  public function toggleSearch(): void
  {
    $this->reset(['search', 'search_pack', 'search_tr', 'search_tr_pack', 'search_ty', 'search_ty_pack', 'paginas']);
    $this->resetPagination();
    $this->toggle = !$this->toggle;
  }

  /**
   * enviar la provision elegida mediante un evento
   * @param Provision $provision
   * @return void
   */
  public function addProvision(Provision $provision): void
  {
    $this->dispatch('add-provision', provision: $provision);
  }

  /**
   * enviar el pack elegido mediante un evento
   * @param Pack $pack
   * @return void
   */
  public function addPack(Pack $pack): void
  {
    $this->dispatch('add-pack', pack: $pack);
  }

  /**
   * buscar suministros para el periodo de peticion
   * con proveedor activo
   * @return mixed
   */
  public function searchProvisions()
  {
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

    return $result;
  }

  /**
   * buscar packs con proveedores activos
   * @return mixed
   */
  public function searchPacks()
  {
    $result = Pack::whereHas('suppliers', function ($query) {
      $query->where('status_is_active', true);
    })
      ->has('provision')  // incluir suministro del pack
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
   * reiniciar la paginacion al buscar
   * @return void
   */
  public function resetPagination()
  {
    $this->resetPage();
  }

  #[On('refresh-search')]
  public function render()
  {
    $provisions = $this->searchProvisions();
    $packs = $this->searchPacks();

    return view('livewire.suppliers.search-provision-period', compact('provisions', 'packs'));
  }
}
