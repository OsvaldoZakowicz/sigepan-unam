<?php

namespace App\Livewire\Stocks;

use App\Models\Product;
use App\Models\Tag;
use Illuminate\Auth\Events\Validated;
use Illuminate\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;
use Livewire\Component;

class EditProduct extends Component
{
  use WithFileUploads;

  public $product;

  public Collection $tags;

  // datos del producto
  public $product_name;
  public $product_short_description;
  public $product_expires_in;
  public $product_in_store;
  public $product_image_path;

  // nueva imagen
  public $new_product_image;

  // tags del producto
  public $selected_id_tag = '';
  public Collection $tags_list;

  /**
   * boot de datos constantes
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
  public function mount(int $id): void
  {
    $this->product = Product::findOrFail($id);

    $this->product_name              = $this->product->product_name;
    $this->product_short_description = $this->product->product_short_description;
    $this->product_expires_in        = $this->product->product_expires_in;
    $this->product_in_store          = $this->product->product_in_store;
    $this->product_image_path        = $this->product->product_image_path;

    // coleccion de tags asignadas al producto
    $this->fill(['tags_list' => collect()]);

    $this->product->tags->each(function ($tag) {
      $this->tags_list->push([
        'tag_id'  =>  'tag_' . $tag->id,
        'tag'     =>  $tag,
      ]);
    });
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
   * editar producto
  */
  public function save()
  {
    $validated = $this->validate([
      'product_name'              => [
        'required',
        Rule::unique('products', 'product_name')->ignore($this->product->id),
        'regex:/^[a-zA-ZáéíóúñÁÉÍÓÚÑ0-9\s,\.]+$/u',
        'min:5',
        'max:50'
      ],
      'product_short_description' => ['required', 'regex:/^[a-zA-ZáéíóúñÁÉÍÓÚÑ0-9\s,\.]+$/u', 'min:15', 'max:150'],
      'product_expires_in'        => ['required'],
      'product_in_store'          => ['required'],
      'product_image_path'        => ['required'],
      'new_product_image'         => ['nullable', 'image', 'max:4096', 'mimes:jpeg,png,jpg'],
      'tags_list'                 => ['required'],
    ], [
      'product_name.unique'       => 'Ya existe un producto con el mismo nombre',
      'product_name.regex'        => ':attribute solo puede tener, letras y numeros',
      'product_name.min'          => ':attribute debe ser de una longitud minima de :min',
      'product_name.max'          => ':attribute debe ser de una longitud maxima de :max',
      'product_short_description.regex' => ':attribute solo puede tener, letras y numeros',
      'product_short_description.min'   => ':attribute debe ser de una longitud minima de :min',
      'product_short_description.max'   => ':attribute debe ser de una longitud maxima de :max',
      'product_in_store.required' => 'indique si desea publicar en la tienda este producto',
      'tags_list.required'        => 'elija al menos una etiqueta que describa el producto',
      'product_expires_in.required'   => 'indique la cantidad de :attribute',
      'new_product_image.image'       => 'la :attribute debe ser una imagen',
      'new_product_image.max'         => 'la :attribute puede ser de hasta 4Mb',
      'new_product_image.mimes'       => 'la :attribute puede ser jpeg, png, jpg',
    ], [
      'product_name'              => 'nombre del producto',
      'product_short_description' => 'descripcion corta',
      'tags_list'                 => 'etiquetas de clasificacion',
      'product_expires_in'        => 'dias de vencimiento',
      'product_in_store'          => 'publicar en tienda',
      'product_image'             => 'imagen del producto',
    ]);

    try {

      // nueva imagen de producto
      if ($validated['new_product_image']) {

        // eliminar imagen anterior
        Storage::delete($this->product->product_image_path);

        // almacenar imagen nueva
        $product_image_path = $this->new_product_image->store('productos', 'public');
        $validated['product_image_path'] = $product_image_path;

      }

      $this->product->product_name              = $validated['product_name'];
      $this->product->product_short_description = $validated['product_short_description'];
      $this->product->product_expires_in        = $validated['product_expires_in'];
      $this->product->product_in_store          = $validated['product_in_store'];
      $this->product->product_image_path        = $validated['product_image_path'];
      $this->product->save();

      // sincronizar tags
      $tags_to_sync = Arr::map($validated['tags_list'], function ($tag) { return $tag['tag']->id; });
      $this->product->tags()->sync($tags_to_sync);

      $this->reset();

      session()->flash('operation-success', toastSuccessBody('producto', 'actualizado'));
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
    return view('livewire.stocks.edit-product');
  }
}
