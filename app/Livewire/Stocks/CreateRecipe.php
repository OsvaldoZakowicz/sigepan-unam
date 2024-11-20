<?php

namespace App\Livewire\Stocks;

use App\Models\Provision;
use Livewire\Component;

class CreateRecipe extends Component
{
  // lista de suministros seleccionada
  public $provisions = [];
  // key autoincremental para el array de suministros seleccionados
  public $provision_array_key;

  // datos generales de la receta
  public $recipe_name;
  public $recipe_short_description;
  public $recipe_production;

  // montar datos
  public function mount()
  {

  }

  //* evento guardar precios
  // notificar a cada componente livewire InputPriceForm
  public function save()
  {
    //no hacer nada si la lista de provisones esta vacia
    if (count($this->provisions) === 0) {
      return;
    }


    // todo: dispatch especifico al componente InputProvisionForm
    // guardar al menos uno
    $this->dispatch('save-prices');

    // todo: dispatch especifico al componente InputProvisionForm
    // refrescar cuadro de busqueda
    $this->dispatch('refresh-search');
  }

  public function render()
  {
    return view('livewire.stocks.create-recipe');
  }
}
