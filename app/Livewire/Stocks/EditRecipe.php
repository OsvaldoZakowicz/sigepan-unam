<?php

namespace App\Livewire\Stocks;

use App\Models\Recipe;
use App\Models\Provision;
use App\Models\Product;
use App\Models\ProvisionCategory;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class EditRecipe extends Component
{
  public $recipe;

  public $recipe_yields;            // rendimiento en unidades
  public $recipe_portions;          // rendimiento en porciones por unidad
  public $recipe_instructions;
  public $recipe_short_description;
  public $time;

  // lista de categoria de suministros
  public Collection $provision_categories;

   /**
   * montar datos
   * @return void
   */
  public function mount(int $id): void
  {
    $this->recipe = Recipe::findOrFail($id);

    $this->recipe_yields = $this->recipe->recipe_yields;
    $this->recipe_portions = $this->recipe->recipe_portions;
    $this->recipe_instructions = $this->recipe->recipe_instructions;
    $this->recipe_short_description = $this->recipe_short_description;
    $this->time = $this->recipe->recipe_preparation_time;

    $this->setProvisionsList();
  }

   /**
   * iniciar una coleccion de categorias de suministro de la receta
   * ['provision_categories' => []]
   * @return void.
   */
  public function setProvisionsList(): void
  {
    $categories_to_edit = $this->recipe->provision_categories->map(
      function ($provision_category) {
        return [
          'category' => $provision_category->load('measure', 'provision_type'),
          'category_id' => 'category_' . $provision_category->id,
          'quantity' => $provision_category->pivot->quantity,
        ];
      }
    );

    $this->fill(['provision_categories' => collect($categories_to_edit)]);
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
        'descr_toast' => 'este suministro ya fue elegido para la receta'
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
      'recipe_yields'        =>  ['required', 'numeric', 'min:1', 'max:99'],
      'recipe_portions'      =>  ['required', 'numeric', 'min:1', 'max:99'],
      'time'                 =>  ['required', 'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/'],
      'recipe_instructions'  =>  ['required', 'max:2000'],
      'provision_categories'            =>  ['required'],
      'provision_categories.*.quantity' =>  ['required', 'numeric', 'min:0.01', 'max:99.99'],
    ], [
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
      'recipe_instructions.max' => 'Las :attribute pueden ser de hasta :max caracteres',
      'provision_categories.required'            => 'La :attribute debe tener al menos un suministro',
      'provision_categories.*.quantity.required' => 'La :attribute es obligatorio',
      'provision_categories.*.quantity.numeric'  => 'La :attribute debe ser un numero',
      'provision_categories.*.quantity.min'      => 'La :attribute debe ser minimo :min',
      'provision_categories.*.quantity.max'      => 'La :attribute debe ser maximo :max',
    ], [
      'recipe_yields'        => 'rendimiento',
      'recipe_portions'      => 'porciones',
      'time'                 => 'tiempo de preparacion',
      'recipe_instructions'  => 'instrucciones de preparacion',
      'provision_categories' => 'lista de suministros',
      'provision_categories.*.quantity' => 'cantidad requerida',
    ]);

    try {

      DB::transaction(function () use ($validated) {

        $this->recipe->recipe_yields = $validated['recipe_yields'];
        $this->recipe->recipe_portions = $validated['recipe_portions'];
        $this->recipe->recipe_preparation_time = $validated['time'];
        $this->recipe->recipe_instructions = $validated['recipe_instructions'];
        $this->recipe->save();
  
        // preparar datos para sync
        $provision_data = collect($validated['provision_categories'])
          ->mapWithKeys(function ($item) {
            return [$item['category']->id => ['quantity' => $item['quantity']]];
          })
          ->toArray(); // retorna[ ['n' => ['quantity' => n]], ..., ]

        // sync() reemplaza completamente (elimina los no presentes, agrega nuevos, actualiza existentes)
        $this->recipe->provision_categories()->sync($provision_data);
          
      });


      session()->flash('operation-success', toastSuccessBody('receta', 'editada'));
      $this->redirectRoute('stocks-recipes-index');

    } catch (\Exception $e) {

      session()->flash('operation-error', 'error: ' . $e->getMessage() . ', contacte con el Administrador');
      $this->redirectRoute('stocks-recipes-index');
    }
  }

  /**
   * Renderizar vista
   * @return \Illuminate\View\View
   */
  public function render()
  {
    return view('livewire.stocks.edit-recipe');
  }
}
