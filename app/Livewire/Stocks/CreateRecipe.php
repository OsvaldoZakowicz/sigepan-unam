<?php

namespace App\Livewire\Stocks;

use App\Models\Provision;
use App\Models\Recipe;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;
use Illuminate\Support\Collection;
use Livewire\Component;

class CreateRecipe extends Component
{
  // datos generales de la receta
  public $recipe_title;
  public $recipe_yields; // rendimiento en unidades
  public $recipe_portions; // rendimiento en porciones por unidad
  public $recipe_instructions;
  public $recipe_short_description;

  // tiempo de preparacion
  public $time_h = '00'; // horas
  public $time_m = '01'; // minutos

  // lista de suministros seleccionada
  public Collection $provisions;

  /**
   * Valida y formatea el tiempo en horas ingresado
   * Asegura que esté entre 00-12 con formato de 2 dígitos
   * @return void
   */
  public function updatedTimeH()
  {
    // Validar rango
    $value = (int) $this->time_h;
    if ($value < 0) {
      $this->time_h = '00';
    } elseif ($value > 12) {
      $this->time_h = '12';
    } else {
      // Formatear con ceros a la izquierda
      $this->time_h = str_pad($value, 2, '0', STR_PAD_LEFT);
    }
  }

  /**
   * Valida y formatea el tiempo en minutos ingresado
   * Asegura que esté entre 00-59 con formato de 2 dígitos
   * @return void
   */
  public function updatedTimeM()
  {
    // Validar rango
    $value = (int) $this->time_m;
    if ($value < 1) {
      $this->time_m = '01';
    } elseif ($value > 59) {
      $this->time_m = '59';
    } else {
      // Formatear con ceros a la izquierda
      $this->time_m = str_pad($value, 2, '0', STR_PAD_LEFT);
    }
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
   * iniciar una coleccion de suministros vacia
   * ['provisions' => []]
   * @return void.
   */
  public function setProvisionsList(): void
  {
    $this->fill(['provisions' => collect()]);
  }

  /**
   * * agregar suministros a la lista de receta
   * provision_id, para mantener el id del suministro en el request
   * * el evento proviene de SearchProvisionRecipe::class
   * @param Provision $provision_to_add un suministro a agregar
   * @return void
   */
  #[On('add-provision')]
  public function addProvisionToList(Provision $provision): void
  {

    if ($this->provisions->contains('provision_id', 'provision_' . $provision->id)) {

      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'este suministro ya fue elegido'
      ]);

      return;
    }

    $this->provisions->push([
      'provision'     =>  $provision,
      'provision_id'  =>  'provision_' . $provision->id,
      'quantity'      =>  '',
    ]);
  }

  /**
   * quitar suministro o pack de la lista
   * @param int $index
   * @return void
   */
  public function removeItemFromList(int $index): void
  {
    $this->provisions->forget($index);
  }

  /**
   * vaciar lista completa
   * @return void
   */
  public function removeAllItemsFromList(): void
  {
    $this->provisions = collect();
  }


  //* guardar receta
  public function save()
  {
    $validated = $this->validate([
      'recipe_title'              =>  ['required', 'unique:recipes,recipe_title', 'regex:/^[\p{L}\s0-9]+$/', 'min:5', 'max:50'],
      'recipe_yields'             =>  ['required', 'numeric', 'min:1', 'max:99'],
      'recipe_portions'           =>  ['required', 'numeric', 'min:1', 'max:99'],
      'time_h'                    =>  ['required', 'numeric', 'min:0', 'max:12'],
      'time_m'                    =>  ['required', 'numeric', 'min:1', 'max:59'],
      'recipe_instructions'       =>  ['required'],
      'provisions'                =>  ['required'],
      'provisions.*.quantity'     =>  ['required', 'numeric', 'min:0.01', 'max:99.99'],
    ], [
      'recipe_title.unique'            => 'Ya existe una receta con el mismo titulo',
      'recipe_title.regex'             => 'El :attribute solo puede tener, letras y numeros',
      'recipe_title.min'               => 'El :attribute debe ser de 5 o mas caracteres',
      'recipe_title.max'               => 'El :attribute puede ser de hasta 50 caracteres',
      'provisions.required'            => 'La :attribute debe tener al menos un suministro',
      'provisions.*.quantity.required' => 'La :attribute es obligatoria',
      'provisions.*.quantity.numeric'  => 'La :attribute debe ser un numero',
      'provisions.*.quantity.min'      => 'La :attribute debe ser minimo :min',
      'provisions.*.quantity.max'      => 'La :attribute debe ser maximo :max',
    ], [
      'recipe_title'            => 'titulo',
      'recipe_yields'           => 'rendimiento',
      'recipe_portions'         => 'porciones',
      'recipe_preparation_time' => 'tiempo de preparación',
      'recipe_instructions'     => 'instrucciones',
      'provisions'              => 'lista de suministros',
      'provisions.*.quantity'   => 'cantidad requerida',
    ]);


    try {

      $hours = str_pad((int) $validated['time_h'], 2, '0', STR_PAD_LEFT);
      $minutes = str_pad((int) $validated['time_m'], 2, '0', STR_PAD_LEFT);
      $validated['recipe_preparation_time'] = "{$hours}:{$minutes}:00";

      $recipe = Recipe::create($validated);

      foreach ($validated['provisions'] as $provision) {
        $recipe->provisions()->attach($provision['provision']->id, ['recipe_quantity' => $provision['quantity']]);
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
