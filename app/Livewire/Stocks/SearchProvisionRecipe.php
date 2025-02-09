<?php

namespace App\Livewire\Stocks;

use App\Models\Provision;
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
   * enviar la provision elegida mediante un evento
   * notifica al componente livewire CreateRecipe::class
   * @param int $id id del suministro
   * @return void
  */
  public function addProvision(Provision $provision): void
  {
    $this->dispatch('add-provision', provision: $provision)
      ->to(CreateRecipe::class);
  }

  /**
   * buscar suministros
   * @return mixed
  */
  public function searchProvisions()
  {
    if ($this->is_editing) {
      // todo: buscar suministros asociados a la receta

    } else {
      // suministros no asociados a la receta
      $provisions = Provision::when($this->search, function ($query) {
          $query->where('provision_name', 'like', '%' . $this->search . '%');
        })
        ->when($this->search_ty, function ($query) {
          $query->where('provision_type_id', $this->search_ty);
        })
        ->orderBy('id', 'desc')
        ->paginate($this->paginas);

      return $provisions;
    }

  }

  #[On('refresh-search')]
  public function render()
  {
    $provisions = $this->searchProvisions();
    return view('livewire.stocks.search-provision-recipe', compact('provisions'));
  }
}
