<?php

namespace App\Livewire\Stocks;

use Livewire\Component;

class EditRecipe extends Component
{

  /**
   * Renderizar vista
   * @return \Illuminate\View\View
   */
  public function render()
  {
    return view('livewire.stocks.edit-recipe');
  }
}
