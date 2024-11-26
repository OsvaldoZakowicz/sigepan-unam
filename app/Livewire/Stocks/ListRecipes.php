<?php

namespace App\Livewire\Stocks;

use App\Models\Recipe;
use Livewire\Component;
use Livewire\WithPagination;

class ListRecipes extends Component
{
  use WithPagination;

  public function render()
  {
    $recipes = Recipe::paginate(10);

    return view('livewire.stocks.list-recipes', compact('recipes'));
  }
}
