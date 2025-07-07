<?php

namespace App\Livewire\Stocks;

use App\Models\Provision;
use App\Models\Recipe;
use App\Models\Product;
use App\Models\ProvisionCategory;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CreateRecipe extends Component
{
  //productos
  public $products;

  // datos generales de la receta
  public $product_id;
  public $recipe_title = '';        // se define a partir de otros inputs
  public $recipe_yields;            // rendimiento en unidades
  public $recipe_portions;          // rendimiento en porciones por unidad
  public $recipe_instructions;
  public $recipe_short_description;
  public $time;

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

  /**
   * al elegir un producto, crear titulo de receta
   * @param $value es el id de producto seleccionado
   */
  public function updatedProductId($value)
  {
    if ($value) {
      $product = collect($this->products)->firstWhere('id', $value);
      if ($product) {
        $this->recipe_title = "receta de " . $product->product_name;
      }
    } else {
      $this->recipe_title = '';
    }
  }



  //* guardar receta
  public function save()
  {
    $validated = $this->validate([
      'product_id'           =>  ['required', 'integer', 'exists:products,id'],
      'recipe_yields'        =>  ['required', 'numeric', 'min:1', 'max:99'],
      'recipe_portions'      =>  ['required', 'numeric', 'min:1', 'max:99'],
      'time'                 =>  ['required', 'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/'],
      'recipe_instructions'  =>  ['required'],
      'provision_categories'            =>  ['required'],
      'provision_categories.*.quantity' =>  ['required', 'numeric', 'min:0.01', 'max:99.99'],
    ], [
      'product_id.required' => 'Debe seleccionar un producto',
      'product_id.integer'  => 'El producto seleccionado no es válido',
      'product_id.exists'   => 'El producto seleccionado no existe',
      'recipe_yields.required' => 'El :attribute es obligatorio',
      'recipe_yields.numeric' => 'El :attribute debe ser un numero',
      'recipe_yields.min' => 'El :attribute debe ser minimo 1',
      'recipe_yields.max' => 'El :attribute debe ser maximo 99',
      'recipe_portions.required' => 'Las :attribute son obligatorias',
      'recipe_portions.numeric' => 'Las :attribute deben ser un numero',
      'recipe_portions.min' => 'Las :attribute deben ser minimo 1',
      'recipe_portions.max' => 'Las :attribute deben ser maximo 99',
      'time.required' => 'El :attribute es obligatirio',
      'time.regex' => 'El :attribute debe ser hh:mm (horas y minutos), ejemplo: 02:30',
      'recipe_instructions.required' => 'Las :attribute son obligatorias',
      'provision_categories.required'            => 'La :attribute debe tener al menos un suministro',
      'provision_categories.*.quantity.required' => 'La :attribute es obligatorio',
      'provision_categories.*.quantity.numeric'  => 'La :attribute debe ser un numero',
      'provision_categories.*.quantity.min'      => 'La :attribute debe ser minimo :min',
      'provision_categories.*.quantity.max'      => 'La :attribute debe ser maximo :max',
    ], [
      'product_id'           => 'producto de la receta',
      'recipe_yields'        => 'rendimiento',
      'recipe_portions'      => 'porciones',
      'time'                 => 'tiempo de preparacion',
      'recipe_instructions'  => 'instrucciones de preparacion',
      'provision_categories' => 'lista de suministros',
      'provision_categories.*.quantity' => 'cantidad requerida',
    ]);

    try {

      $title = $this->recipe_title . ' por ' . $validated['recipe_yields'];
      if (Recipe::where('recipe_title', $title)->exists()) {
        
        $this->addError('recipe_title', 'Ya existe una receta con este título y rendimiento');
        return;
      }

      DB::transaction(function () use ($validated, $title) {

        $recipe = Recipe::create([
          'recipe_title'            => $title,
          'recipe_yields'           => $validated['recipe_yields'],
          'recipe_portions'         => $validated['recipe_portions'],
          'recipe_preparation_time' => $validated['time'],
          'recipe_instructions'     => $validated['recipe_instructions'],
          'product_id'              => $validated['product_id'],
        ]);
  
        foreach ($validated['provision_categories'] as $item) {
          $recipe->provision_categories()->attach($item['category']->id, ['quantity' => $item['quantity']]);
        }
          
      });


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
