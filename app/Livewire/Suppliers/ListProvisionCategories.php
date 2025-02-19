<?php

namespace App\Livewire\Suppliers;

use App\Models\ProvisionCategory;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\View\View;
use Livewire\Component;

class ListProvisionCategories extends Component
{
  use WithPagination;

  #[Url]
  public $search_input = '';

  /**
   * Obtiene todas las categorías de provisión paginadas
   * @return mixed
   */
  public function searchProvisionCategories()
  {
    $categories = ProvisionCategory::with('measure', 'provision_type')
      ->when($this->search_input, function($query) {
        $query->where('provision_category_name', 'like', '%' . $this->search_input . '%');
      })
      ->orderBy('id', 'desc')
      ->paginate(10);

    return $categories;
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
   * redirigir a la edicion de categoria
   * solo si la categoria puede editarse
   * solo si la categoria no se usa en suministros
   * @param ProvisionCategory $category
   */
  public function edit(ProvisionCategory $category)
  {
    if (!$category->provision_category_is_editable) {

      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'info',
        'title_toast' =>  toastTitle('', true),
        'descr_toast' =>  'No se puede editar la categoria, la misma es propia del sistema',
      ]);

      return;
    }

    if ($category->provisions->count() > 0) {

      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'info',
        'title_toast' =>  toastTitle('', true),
        'descr_toast' =>  'No se puede editar la categoria, la misma se usa en suministros',
      ]);

      return;
    }

    $this->redirectRoute('suppliers-categories-edit', $category->id, true, true);
  }

  /**
   * eliminar la categoria
   * solo si la categoria puede editarse
   * solo si la categoria no se usa en suministros
   * @param ProvisionCategory $category
   */
  public function delete(ProvisionCategory $category)
  {
    if (!$category->provision_category_is_editable) {
      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'info',
        'title_toast' =>  toastTitle('', true),
        'descr_toast' =>  'No se puede eliminar la categoria, la misma es propia del sistema',
      ]);
      return;
    }

    if ($category->provisions->count() > 0) {
      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'info',
        'title_toast' =>  toastTitle('', true),
        'descr_toast' =>  'No se puede eliminar la categoria, la misma se usa en suministros',
      ]);
      return;
    }

    $category->delete();

    $this->dispatch('toast-event', toast_data: [
      'event_type'  =>  'success',
      'title_toast' =>  toastTitle('', true),
      'descr_toast' =>  'Categoría eliminada correctamente',
    ]);
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    $categories = $this->searchProvisionCategories();
    return view('livewire.suppliers.list-provision-categories', compact('categories'));
  }
}
