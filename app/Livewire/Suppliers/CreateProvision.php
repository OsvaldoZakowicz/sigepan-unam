<?php

namespace App\Livewire\Suppliers;

use App\Models\Provision;
use App\Models\ProvisionCategory;
use App\Models\ProvisionTrademark;
use Illuminate\View\View;
use Livewire\Component;

class CreateProvision extends Component
{
  public $categories;
  public $trademarks;

  public $provision_category_id;
  public $provision_trademark_id;
  public $provision_type;
  public $measure;
  public $input_quantity_placeholder;
  public $provision_quantity;
  public $provision_short_description;

  public $pack_units;
  public $packs;

  /**
   * preparar constantes
   * @return void
  */
  public function boot(): void
  {
    $this->categories = ProvisionCategory::all();
    $this->trademarks = ProvisionTrademark::all();
  }

  /**
   * montar datos
   * @return void
  */
  public function mount(): void
  {
    $this->packs = collect();
  }

  /**
   * Actualiza los campos relacionados cuando cambia la categoría seleccionada
   * Obtiene la unidad de medida y el tipo de suministro asociados a la categoría
   * @return void
  */
  public function updatedProvisionCategoryId()
  {
    if ($this->provision_category_id) {

      $category = ProvisionCategory::findOrFail($this->provision_category_id);

      $this->measure = $category->measure->unit_name;
      $this->provision_type = $category->provision_type->provision_type_name;

      // Si la medida es 'unidad', asignar 1 a provision_quantity
      if ($category->measure->unit_name === 'unidad') {
        $this->provision_quantity = 1;
      }

      // placeholder de la cantidad esperada
      $this->input_quantity_placeholder = 'cantidad en ' . $category->measure->unit_name .
        ($category->measure->conversion_unit ? ' o ' . $category->measure->conversion_unit : '');

    } else {

      $this->measure                    = '';
      $this->input_quantity_placeholder = '';
      $this->provision_type             = '';
      $this->provision_quantity         = null;

    }
  }

  /**
   * agregar cantidad de packs a la lista
   * por cada cantidad se crea un pack del suministro con dicha cantidad.
   * @return void
  */
  public function addPackUnits(): void
  {

    if ($this->pack_units == 0 || $this->pack_units == null) {
      return;
    }

    if ($this->packs->contains($this->pack_units)) {

      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'esta opcion ya fue elegida'
      ]);

      return;
    }

    $this->packs->prepend($this->pack_units);
    $this->reset('pack_units');
  }

  /**
   * remover cantidad de packs de la lista
   * @param int $index indice del elemento a remover
   * @return void
  */
  public function removePackUnits($index)
  {
    $this->packs->forget($index);
  }

  /**
   * guardar suministro
   * @return void
  */
  public function save()
  {
    $validated = $this->validate([
      'provision_category_id'       =>  ['required'],
      'provision_trademark_id'      =>  ['required'],
      'provision_quantity'          =>  ['required', 'numeric', 'min:0.1', 'max:99'],
      'provision_short_description' =>  ['nullable', 'string', 'min:15', 'max:250'],
    ],[
      'provision_category_id.required'  => 'elija una :attribute para el suministro',
      'provision_trademark_id.required' => 'elija una :attribute para el suministro',
      'provision_quantity.required'     => 'la :attribute es obligatoria',
      'provision_quantity.numeric'      => 'la :attribute debe ser un numero',
      'provision_quantity.min'          => 'la :attribute puede ser de minimo :min',
      'provision_quantity.max'          => 'la :attribute puede ser de maximo :max',
      'provision_short_description.string' => 'la :attribute solo puede ser texto',
      'provision_short_description.min' => 'la :attribute debe tener como minimo :min caracteres',
      'provision_short_description.max' => 'la :attribute debe tener como maximo :max caracteres',
    ],[
      'provision_category_id'       => 'categoria',
      'provision_trademark_id'      => 'marca',
      'provision_quantity'          => 'cantidad de la unidad',
      'provision_short_description' => 'descripcion',
    ]);

    try {

      $category  = ProvisionCategory::findOrFail($validated['provision_category_id']);
      $trademark = ProvisionTrademark::findOrFail($validated['provision_trademark_id']);

      // si ya existe un suministro (incluso borrado) con la categoria, marca y volumen indicado, no se puede crear
      $exist_provision = Provision::withTrashed()
        ->where('provision_category_id', $category->id)
        ->where('provision_trademark_id', $trademark->id)
        ->where('provision_quantity', $validated['provision_quantity'])
        ->first();

      if ($exist_provision) {
        $this->addError('provision_unique', 'Ya existe un suministro con la categoria, marca y volumen ingresado.');
        return;
      }

      $validated['provision_type_id'] = $category->provision_type->id;
      $validated['measure_id']        = $category->measure->id;
      $validated['provision_name']    = $category->provision_category_name;

      // suministro
      $provision = Provision::create($validated);

      // packs
      if (count($this->packs) > 0) {
        foreach ($this->packs as $pack) {
          $provision->packs()->create([
            'pack_name'     => 'pack de ' . $provision->provision_name . ' x ' . $pack,
            'pack_units'    => $pack,
            'pack_quantity' => $provision->provision_quantity * $pack
          ]);
        }
      }

      $this->reset();

      session()->flash('operation-success', toastSuccessBody('suministro', 'creado'));
      $this->redirectRoute('suppliers-provisions-index');

    } catch (\Exception $e) {

      session()->flash('operation-error', 'error: ' . $e->getMessage() . ' contacte al Administrador.');
      $this->redirectRoute('suppliers-provisions-index');

    }

  }

  /**
   * renderizar vista
   * @return View
  */
  public function render(): View
  {
    return view('livewire.suppliers.create-provision');
  }
}
