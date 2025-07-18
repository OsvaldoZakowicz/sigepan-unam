<?php

namespace App\Livewire\Stocks;

use App\Models\Provision;
use App\Models\ProvisionCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use App\Models\ProvisionTrademark;
use App\Models\ProvisionType;
use App\Models\Recipe;

class SearchProvisionRecipe extends Component
{
  use WithPagination;

  #[Url]
  public $search = '';

  #[Url]
  public $search_ty = '';

  #[Url]
  public $paginas = '5';

  // tipos de suministros
  public $provision_types;

  /**
   * montar datos
   * @return void
  */
  public function mount(): void
  {
    $this->provision_types = ProvisionType::all();
  }

  /**
   * reiniciar la paginacion al buscar
   * @return void
  */
  public function resetPagination(): void
  {
    $this->resetPage();
  }

  /**
   * limpiar filtros de busqueda
   * @return void
   */
  public function clearFilters(): void
  {
    $this->reset(['search', 'search_ty', 'paginas']);
  }

  /**
   * enviar la categoria elegida mediante un evento
   * notifica al componente livewire CreateRecipe::class
   * @param int $id id de la categoria
   * @return void
  */
  public function addProvisionCategory(int $id): void
  {
    $this->dispatch('add-provision-category', category_id: $id);
  }

  /**
   * buscar categorias de suministros
   * siempre recupera todas las categorias
   * @return mixed
  */
  public function searchProvisionCategories()
  {
    $provisions_categories = ProvisionCategory::when($this->search, function ($query) {
        $query->where('provision_category_name', 'like', '%' . $this->search . '%');
      })
      ->when($this->search_ty, function ($query) {
        $query->where('provision_type_id', $this->search_ty);
      })
      ->orderBy('id', 'desc')
      ->paginate($this->paginas);

    return $provisions_categories;

  }

  #[On('refresh-search')]
  public function render()
  {
    $provisions_categories = $this->searchProvisionCategories();
    return view('livewire.stocks.search-provision-recipe', compact('provisions_categories'));
  }
}
