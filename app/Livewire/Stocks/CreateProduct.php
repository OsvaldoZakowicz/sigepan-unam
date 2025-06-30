<?php

namespace App\Livewire\Stocks;

use App\Models\Tag;
use App\Models\Product;
use App\Services\Product\ProductService;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\WithFileUploads;
use Livewire\Component;

class CreateProduct extends Component
{
  use WithFileUploads;

  public Collection $tags;

  // datos del producto
  public $product_name;
  public $product_short_description;
  public $product_expires_in;
  public $product_in_store;
  public $product_image;

  // precios del producto
  public $prices_list = [];
  public $quantity;
  public $price;
  public $price_description;
  public $is_default = false;

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
   * Agregar un precio a la lista
   */
  public function addPrice(): void
  {
    // Validaciones básicas
    $this->validate([
      'quantity' => [
        'required',
        'integer',
        'min:1',
        function ($attribute, $value, $fail) {
          // Verifica si ya existe esta cantidad en la lista temporal
          $quantityExists = collect($this->prices_list)->contains(function ($price) use ($value) {
            return $price['quantity'] === (int)$value;
          });

          if ($quantityExists) {
            $fail('Ya existe un precio para esta cantidad.');
          }
        }
      ],
      'price' => ['required', 'numeric', 'regex:/^\d{1,6}(\.\d{1,2})?$/', 'min:1'],
      'price_description' => [
        'required',
        'string',
        'min:3',
        function ($attribute, $value, $fail) {
          // Verifica si ya existe esta descripción en la lista temporal
          $descriptionExists = collect($this->prices_list)->contains(function ($price) use ($value) {
            return strtolower($price['description']) === strtolower($value);
          });

          if ($descriptionExists) {
            $fail('Ya existe un precio con esta descripción.');
          }
        }
      ],
    ]);

    // Control del precio predeterminado
    if ($this->is_default) {
      foreach ($this->prices_list as &$price) {
        if ($price['is_default']) {
          $price['is_default'] = false;
        }
      }
    } else if (empty($this->prices_list)) {
      $this->is_default = true;
    }

    // Agregar el nuevo precio a la lista
    $this->prices_list[] = [
      'quantity' => (int)$this->quantity,
      'price' => (float)$this->price,
      'description' => $this->price_description,
      'is_default' => $this->is_default,
    ];

    // Ordenar la lista por cantidad
    usort($this->prices_list, function ($a, $b) {
      return $a['quantity'] <=> $b['quantity'];
    });

    // Limpiar el formulario
    $this->reset(['quantity', 'price', 'price_description', 'is_default']);

    // Notificar éxito
    $this->dispatch('toast-event', toast_data: [
      'event_type' => 'success',
      'title_toast' => toastTitle('', true),
      'descr_toast' => 'Precio agregado correctamente'
    ]);
  }

  /**
   * Remover un precio de la lista
   */
  public function removePrice($index): void
  {
    unset($this->prices_list[$index]);
    $this->prices_list = array_values($this->prices_list);
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
      'product_expires_in'        => ['required'],
      'product_in_store'          => ['required'],
      'product_image'             => ['required', 'image', 'max:4096', 'mimes:jpeg,png,jpg'],
      'tags_list'                 => ['required'],
      'prices_list'               => ['required', 'array', 'min:1'],
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
      'product_expires_in.required' => 'indique la cantidad de :attribute',
      'product_image.required'    => 'la :attribute es obligatoria para publicar el producto',
      'product_image.image'       => 'la :attribute debe ser una imagen',
      'product_image.max'         => 'la :attribute puede ser de hasta 4Mb',
      'product_image.mimes'       => 'la :attribute puede ser jpeg, png, jpg',
    ], [
      'product_name'              => 'nombre del producto',
      'product_short_description' => 'descripcion corta',
      'tags_list'                 => 'etiquetas de clasificacion',
      'product_expires_in'        => 'dias de vencimiento',
      'product_in_store'          => 'publicar en tienda',
      'product_image'             => 'imagen del producto',
    ]);

    $defaultPrices = collect($this->prices_list)->filter(function ($price) {
      return $price['is_default'];
    })->count();

    if ($defaultPrices !== 1) {
      $this->addError('prices_list', 'Debe haber exactamente un precio predeterminado');
      return;
    }

    try {

      $product_image_path = $this->product_image->store('productos', 'public');
      $validated['product_image_path'] = $product_image_path;

      $productService = new ProductService();
      $productService->createProduct($validated, $this->prices_list);

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
