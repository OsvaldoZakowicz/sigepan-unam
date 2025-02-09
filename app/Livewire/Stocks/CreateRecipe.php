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
  // rendimiento en unidades
  public $recipe_yields;
  // rendimiento en porciones por unidad
  public $recipe_portions;
  public $recipe_preparation_time;
  public $recipe_instructions;
  public $recipe_short_description;

  // lista de suministros seleccionada
  public Collection $provisions;

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
      'recipe_title'              =>  ['required'],
      'recipe_yields'             =>  ['required'],
      'recipe_portions'           =>  ['required'],
      'recipe_preparation_time'   =>  ['required'],
      'recipe_instructions'       =>  ['required'],
      'recipe_short_description'  =>  ['nullable'],
      'provisions'                =>  ['required'],
      'provisions.*.quantity'     =>  ['required', 'numeric', 'min:0.01', 'max:99.99'],
    ],[
      'provisions.required'            => 'La :attribute debe tener al menos un suministro',
      'provisions.*.quantity.required' => 'La :attribute es obligatoria',
      'provisions.*.quantity.numeric'  => 'La :attribute debe ser un numero',
      'provisions.*.quantity.min'      => 'La :attribute debe ser minimo :min',
      'provisions.*.quantity.max'      => 'La :attribute debe ser maximo :max',
    ],[
      'recipe_title'            => 'titulo',
      'recipe_yields'           => 'rendimiento',
      'recipe_portions'         => 'porciones',
      'recipe_preparation_time' => 'tiempo de preparación',
      'recipe_instructions'     => 'instrucciones',
      'provisions'              => 'lista de suministros',
      'provisions.*.quantity'   => 'cantidad requerida',
    ]);

    dd($validated);

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
