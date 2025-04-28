<?php

namespace App\Livewire\Stocks;

use App\Models\Provision;
use App\Models\Recipe;
use App\Models\Product;
use App\Models\ProvisionCategory;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;
use Illuminate\Support\Collection;
use Livewire\Component;

class CreateRecipe extends Component
{
  //productos
  public $products;

  // datos generales de la receta
  public $product_id;
  public $recipe_title;
  public $recipe_yields; // rendimiento en unidades
  public $recipe_portions; // rendimiento en porciones por unidad
  public $recipe_instructions;
  public $recipe_short_description;

  // tiempo de preparacion
  public $time_h = '00'; // horas
  public $time_m = '01'; // minutos

  // lista de categoria de suministros
  public Collection $provision_categories;

  /**
   * boot de datos
   * @return void
  */
  public function boot(): void
  {
    $this->products = Product::all();
  }

  /**
   * montar datos
   * @return void
   */
  public function mount()
  {
    $this->setProvisionsList();
  }

  /**
   * iniciar una coleccion de categorias de suministro vacia
   * ['provision_categories' => []]
   * @return void.
   */
  public function setProvisionsList(): void
  {
    $this->fill(['provision_categories' => collect()]);
  }

  /**
   *
   * @param int $category_id id de la categoria a agregar
   * @return void
   */
  #[On('add-provision-category')]
  public function addProvisionCategoryToList(int $category_id): void
  {

    $category = ProvisionCategory::findOrFail($category_id);

    if ($this->provision_categories->contains('category_id', 'category_' . $category->id)) {

      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'este categoria de suministros ya fue elegida'
      ]);

      return;
    }

    $this->provision_categories->push([
      'category'     =>  $category->load('measure', 'provision_type'),
      'category_id'  =>  'category_' . $category->id,
      'quantity'     =>  '',
    ]);
  }

  /**
   * quitar suministro o pack de la lista
   * @param int $index
   * @return void
   */
  public function removeItemFromList(int $index): void
  {
    $this->provision_categories->forget($index);
  }

  /**
   * vaciar lista completa
   * @return void
   */
  public function removeAllItemsFromList(): void
  {
    $this->provision_categories = collect();
  }

  //* guardar receta
  public function save()
  {
    $validated = $this->validate([
      'product_id'                =>  ['required'],
      'recipe_title'              =>  ['required', 'unique:recipes,recipe_title', 'regex:/^[\p{L}\s0-9]+$/', 'min:5', 'max:50'],
      'recipe_yields'             =>  ['required', 'numeric', 'min:1', 'max:99'],
      'recipe_portions'           =>  ['required', 'numeric', 'min:1', 'max:99'],
      'time_h'                    =>  ['required', 'numeric', 'min:0', 'max:12'],
      'time_m'                    =>  ['required', 'numeric', 'min:1', 'max:59'],
      'recipe_instructions'       =>  ['required'],
      'provision_categories'                =>  ['required'],
      'provision_categories.*.quantity'     =>  ['required', 'numeric', 'min:0.01', 'max:99.99'],
    ], [
      'product_id.required'            => ':attribute es obligatorio',
      'recipe_title.unique'            => 'Ya existe una receta con el mismo titulo',
      'recipe_title.regex'             => ':attribute solo puede tener, letras y numeros',
      'recipe_title.min'               => ':attribute debe ser de 5 o mas caracteres',
      'recipe_title.max'               => ':attribute puede ser de hasta 50 caracteres',
      'provision_categories.required'            => ':attribute debe tener al menos un suministro',
      'provision_categories.*.quantity.required' => ':attribute es obligatorio',
      'provision_categories.*.quantity.numeric'  => ':attribute debe ser un numero',
      'provision_categories.*.quantity.min'      => ':attribute debe ser minimo :min',
      'provision_categories.*.quantity.max'      => ':attribute debe ser maximo :max',
    ], [
      'product_id'              => 'producto de la receta',
      'recipe_title'            => 'titulo',
      'recipe_yields'           => 'rendimiento',
      'recipe_portions'         => 'porciones',
      'recipe_preparation_time' => 'tiempo de preparación',
      'recipe_instructions'     => 'instrucciones',
      'provision_categories'              => 'lista de suministros',
      'provision_categories.*.quantity'   => 'cantidad requerida',
    ]);

    try {

      $hours    = str_pad((int) $validated['time_h'], 2, '0', STR_PAD_LEFT);
      $minutes  = str_pad((int) $validated['time_m'], 2, '0', STR_PAD_LEFT);

      $recipe = Recipe::create([
        'recipe_title'            => $validated['recipe_title'],
        'recipe_yields'           => $validated['recipe_yields'],
        'recipe_portions'         => $validated['recipe_portions'],
        'recipe_preparation_time' => "{$hours}:{$minutes}:00",
        'recipe_instructions'     => $validated['recipe_instructions'],
        'product_id'              => $validated['product_id'],
      ]);

      foreach ($validated['provision_categories'] as $item) {
        $recipe->provision_categories()->attach($item['category']->id, ['quantity' => $item['quantity']]);
      }

      session()->flash('operation-success', toastSuccessBody('receta', 'creada'));
      $this->redirectRoute('stocks-recipes-index');

    } catch (\Exception $e) {

      session()->flash('operation-error', 'error: ' . $e->getMessage() . ', contacte con el Administrador');
      $this->redirectRoute('stocks-recipes-index');
    }
  }

  public function render()
  {
    return view('livewire.stocks.create-recipe');
  }
}
