<?php

namespace App\Livewire\Stocks;

use App\Models\Tag;
use Livewire\WithPagination;
use Illuminate\View\View;
use Livewire\Component;

class ListTags extends Component
{
  use WithPagination;

  public string $search_input = '';

  /**
   * Busca etiquetas según el criterio de búsqueda
   * @return mixed
   */
  public function searchTags()
  {
    return Tag::query()
      ->when($this->search_input, function ($query) {
        $query->where('tag_name', 'like', '%' . $this->search_input . '%');
      })
      ->orderBy('id', 'desc')
      ->paginate(10);
  }

  /**
   * Editar una etiqueta
   * @param Tag $tag Etiqueta a editar
   * @return void
   */
  public function edit(Tag $tag): void
  {
    if ($tag->products->count() > 0) {

      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'info',
        'title_toast' =>  toastTitle('', true),
        'descr_toast' =>  'No se puede editar la etiqueta de producto, la misma se usa en productos',
      ]);

      return;
    }

    $this->redirectRoute('stocks-tags-edit', $tag->id, true, true);
  }

  /**
   * Eliminar una etiqueta
   * @param Tag $tag Etiqueta a eliminar
   * @return void
   */
  public function delete(Tag $tag): void
  {
    if ($tag->products->count() > 0) {

      $this->dispatch('toast-event', toast_data: [
        'event_type'  =>  'info',
        'title_toast' =>  toastTitle('', true),
        'descr_toast' =>  'No se puede eliminar la etiqueta de productos, la misma se usa en productos',
      ]);

      return;
    }

    $tag->delete();

    $this->dispatch('toast-event', toast_data: [
      'event_type'  =>  'success',
      'title_toast' =>  toastTitle('exitosa'),
      'descr_toast' =>  toastSuccessBody('etiqueta', 'eliminada'),
    ]);
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    $tags = $this->searchTags();
    return view('livewire.stocks.list-tags', compact('tags'));
  }
}
