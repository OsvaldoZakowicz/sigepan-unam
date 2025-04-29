<?php

namespace App\Livewire\Stocks;

use App\Models\Recipe;
use Illuminate\View\View;
use Livewire\Component;

class ShowRecipe extends Component
{

  public Recipe $recipe;

  /**
   * montar datos
   * @param int $id de receta
   * @return void
   */
  public function mount($id): void
  {
    $this->recipe = Recipe::findOrFail($id)->load(['provision_categories', 'product']);
    //dd($this->recipe);
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    return view('livewire.stocks.show-recipe');
  }
}
