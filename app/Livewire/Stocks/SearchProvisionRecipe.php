<?php

namespace App\Livewire\Stocks;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use App\Models\ProvisionTrademark;
use App\Models\ProvisionType;

class SearchProvisionRecipe extends Component
{
  use WithPagination;

  #[Url]
  public $search = '';
  #[Url]
  public $search_tr = '';
  #[Url]
  public $search_ty = '';

  // marcas de suministros
  public $trademarks;
  // tipos de suministros
  public $provision_types;

  // busqueda de edicion
  public $is_editing;
  public $recipe_id;

  // montar datos
  public function mount($recipe_id = null, $is_editing = false)
  {
    $this->trademarks = ProvisionTrademark::all();
    $this->provision_types = ProvisionType::all();
    $this->is_editing = $is_editing;
    $this->recipe_id = $recipe_id;
  }

  // reiniciar la paginacion al buscar
  public function resetPagination()
  {
    $this->resetPage();
  }

  //* enviar la provision elegida mediante un evento
  // notifica al componente livewire CreateRecipe
  public function addProvision($id)
  {
    $this->dispatch('append-provision', id: $id)->to(CreateRecipe::class);
  }

  // buscar suministros
  public function searchProvisions()
  {
    if ($this->is_editing) {
      // todo: buscar suministros asociados a la receta

    } else {
      // todo: buscar suministros no asociados a la receta

    }

  }

  #[On('refresh-search')]
  public function render()
  {
    // todo: buscar y enviar a la vista

    return view('livewire.stocks.search-provision-recipe');
  }
}
