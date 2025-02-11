<?php

namespace App\Livewire\Stocks;

use App\Models\Tag;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class CreateProduct extends Component
{
  public Collection $tags;

  // datos del producto
  public $product_name;
  public $product_short_description;
  public $product_price;

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
   * quitar una etiqueta de la lista
   * @param int $key posicion del elemento a eliminar
   * @return void
  */
  public function removeTagFromList(int $key): void
  {
    $this->tags_list->forget($key);
  }

  /**
   * guardar producto
  */
  public function save()
  {
    $validated = $this->validate([
      'product_name'              => ['required', 'unique:recipes,recipe_title', 'regex:/^[a-zA-ZáéíóúñÁÉÍÓÚÑ0-9\s,\.]+$/u', 'min:5', 'max:50'],
      'product_short_description' => ['required', 'regex:/^[a-zA-ZáéíóúñÁÉÍÓÚÑ0-9\s,\.]+$/u', 'min:15', 'max:150'],
      'product_price'             => ['required', 'numeric', 'regex:/^\d{1,6}(\.\d{1,2})?$/', 'min:1'],
      'tags_list'                 => ['required'],
    ], [
      '*.required'                => ':attribute es obligatorio',
      'product_name.unique'       => 'Ya existe un producto con el mismo nombre',
      'product_name.regex'        => ':attribute solo puede tener, letras y numeros',
      'product_name.min'          => ':attribute debe ser de una longitud minima de :min',
      'product_name.max'          => ':attribute debe ser de una longitud maxima de :max',
      'product_short_description.regex' => ':attribute solo puede tener, letras y numeros',
      'product_short_description.min'   => ':attribute debe ser de una longitud minima de :min',
      'product_short_description.max'   => ':attribute debe ser de una longitud maxima de :max',
      'product_price.required'    => ':attribute es obligatorio',
      'product_price.numeric'     => ':attribute es debe ser un número',
      'product_price.min'         => ':attribute puede ser de minimo $1',
      'product_price.regex'       => ':attribute puede ser de hasta $999999.99',
      'tags_list'                 => 'Elija al menos una etiqueta que describa el producto',
    ], [
      'product_name'              => 'nombre del producto',
      'product_short_description' => 'descripcion corta',
      'product_price'             => 'precio del producto',
      'tags_list'                 => 'etiquetas de clasificacion',
    ]);

    try {

      $product = Product::create($validated);

      foreach ($validated['tags_list'] as $tag_item) {
        $product->tags()->attach($tag_item['tag']->id);
      }

      $this->reset();

      session()->flash('operation-success', toastSuccessBody('producto', 'creado'));
      $this->redirectRoute('stocks-products-index');

    } catch (\Exception $e) {

      session()->flash('operation-error', 'error: ' . $e->getMessage() . ' contacte al Administrador.');
      $this->redirectRoute('stocks-products-index');

    }
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
