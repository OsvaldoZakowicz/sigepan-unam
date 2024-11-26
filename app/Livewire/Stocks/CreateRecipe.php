<?php

namespace App\Livewire\Stocks;

use App\Models\Provision;
use App\Models\Recipe;
use Illuminate\Support\Carbon;
use Livewire\Component;

class CreateRecipe extends Component
{
  // lista de suministros seleccionada
  public $provisions = [];
  // key autoincremental para el array de suministros seleccionados
  public $provision_array_key;

  // datos generales de la receta
  public $recipe_title;
  public $recipe_yields; // rendimiento en unidades
  public $recipe_portions; // rendimiento en porciones
  public $recipe_preparation_time;
  public $recipe_instructions;
  public $recipe_short_description;

  // montar datos
  public function mount()
  {

  }

  //* guardar receta
  public function save()
  {
    $validated = $this->validate([

      'recipe_title'              =>  ['required'],
      'recipe_yields'             =>  ['required'],
      'recipe_portions'           =>  ['required'],
      'recipe_preparation_time'   =>  ['required'],
      'recipe_instructions'       =>  ['required'],
      'recipe_short_description'  =>  ['nullable'],

    ], [], [
      'recipe_title' => 'titulo',
      'recipe_yields' => 'rendimiento',
      'recipe_portions' => 'porciones',
      'recipe_preparation_time' => 'tiempo de preparación',
      'recipe_instructions' => 'instrucciones',
      'recipe_short_description' => 'notas adicionales',
    ]);

    //dd($validated['recipe_preparation_time']);

    $validated['recipe_preparation_time'] = sprintf(
      '%02d:%02d:%02d',
      floor($validated['recipe_preparation_time'] / 60),   // Horas
      $validated['recipe_preparation_time'] % 60,           // Minutos restantes
      0                             // Segundos (en este ejemplo, siempre 0)
    );

    //dd($validated['recipe_preparation_time']);

    try {

      Recipe::create($validated);

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
