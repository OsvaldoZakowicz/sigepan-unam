<?php

namespace App\Livewire\Stocks;

use App\Models\Recipe;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class ListRecipes extends Component
{
  use WithPagination;

  #[Url]
  public string $search_recipe = '';

  public function searchRecipes()
  {
    return Recipe::withTrashed()
      ->when($this->search_recipe, function ($query) {
        $query->where('recipe_title', 'like', '%' . $this->search_recipe . '%');
      })
      ->orderBy('deleted_at', 'asc') // primero los NO borrados
      ->orderBy('id', 'desc')
      ->paginate(10);
  }

  /**
   * resetear la paginacion
   * @return void
   */
  public function resetPagination(): void
  {
    $this->resetPage();
  }

  /**
   * limpiar campos de busqueda
   * @return void
   */
  public function resetSearchInputs(): void
  {
    $this->reset(['search_recipe']);
  }

  public function delete(int $id): void
  {
    try {

      $recipe = Recipe::findOrFail($id);

      $recipe->delete();

      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'success',
        'title_toast' =>  toastTitle('exitosa'),
        'descr_toast' =>  'La Receta fue eliminada.'
      ]);
    } catch (\Exception $e) {

      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'error',
        'title_toast' =>  toastTitle('fallida'),
        'descr_toast' =>  'La Receta no pudo eliminarse debido a: ' . $e->getMessage() . ' contacte al administrador.'
      ]);
    }
  }

  public function restore(int $id)
  {
    try {

      $recipe = Recipe::withTrashed()->findOrFail($id);

      $recipe->restore();

      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'success',
        'title_toast' =>  toastTitle('exitosa'),
        'descr_toast' =>  'La Receta fue recuperada.'
      ]);
    } catch (\Exception $e) {

      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'error',
        'title_toast' =>  toastTitle('fallida'),
        'descr_toast' =>  'La Receta no pudo recuperarse debido a: ' . $e->getMessage() . ' contacte al administrador.'
      ]);
    }
  }

  public function render()
  {
    $recipes = $this->searchRecipes();
    return view('livewire.stocks.list-recipes', compact('recipes'));
  }
}
