<?php

namespace App\Livewire\Stocks;

use Livewire\Component;

class ListRecipes extends Component
{
  public $recipes = [];

  public function render()
  {
    return view('livewire.stocks.list-recipes');
  }
}
