<?php

namespace App\Livewire\Stocks;

use App\Models\Tag;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class CreateProduct extends Component
{
  public Collection $tags;

  // datos del producto
  public string $product_name;
  public string $product_short_description;
  public float $product_price;

  // tags del producto
  public $selected_id_tag = '';
  public Collection $tags_list;

  /**
   * boot de datos
   * @return void
   */
  public function boot(): void
  {
    $this->tags = Tag::all();
  }

  /**
   * montar datos
   * @return void
   */
  public function mount()
  {
    $this->fill(['tags_list' => collect()]);
  }

  /**
   * agregar un tag a la lista
   * @return void
   */
  public function addTagToList(): void
  {
    if ($this->selected_id_tag === '') {
      return;
    }

    $tag = Tag::findOrFail($this->selected_id_tag);

    if ($this->tags_list->contains('tag_id', 'tag_' . $tag->id)) {

      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'esta etiqueta ya fue elegida'
      ]);

      return;
    } else {

      $this->tags_list->push([
        'tag_id'  =>  'tag_' . $tag->id,
        'tag'     =>  $tag,
      ]);
    }

    $this->reset('selected_id_tag');
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    return view('livewire.stocks.create-product');
  }
}
