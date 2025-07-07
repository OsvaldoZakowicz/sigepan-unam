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

  // busqueda de edicion
  public $is_editing;
  public $recipe;

  /**
   * montar datos
   * @param int $recipe_id id de receta
   * @param bool $is_editing indica el modo de busqueda
   * @return void
  */
  public function mount($recipe_id = null, $is_editing = false): void
  {
    $this->provision_types = ProvisionType::all();

    if ($recipe_id) {
      $this->recipe = Recipe::findOrFail($recipe_id);
      $this->is_editing = $is_editing;
    }
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
    $this->dispatch('add-provision-category', category_id: $id)
      ->to(CreateRecipe::class);
  }

  /**
   * buscar categorias de suministros
   * @return mixed
  */
  public function searchProvisionCategories()
  {
    if ($this->is_editing) {
      // todo: buscar categorias asociadas a la receta

    } else {
      // categorias de suministros no asociados a la receta
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

  }

  #[On('refresh-search')]
  public function render()
  {
    $provisions_categories = $this->searchProvisionCategories();
    return view('livewire.stocks.search-provision-recipe', compact('provisions_categories'));
  }
}
