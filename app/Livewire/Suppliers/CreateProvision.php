<?php

namespace App\Livewire\Suppliers;

use App\Models\Measure;
use App\Models\Provision;
use App\Models\ProvisionTrademark;
use App\Models\ProvisionType;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Livewire\Component;

class CreateProvision extends Component
{
  public $trademarks;
  public $measures;
  public $provision_types;

  public $provision_name;
  public $provision_quantity;
  public $provision_short_description = 'sin descripcion';
  public $provision_trademark_id;
  public $provision_type_id;
  public $measure_id;

  public $pack_units;
  public $packs;

  /**
   * preparar constantes
   * @return void
  */
  public function boot(): void
  {
    $this->trademarks = ProvisionTrademark::all();
    $this->measures = Measure::all();
    $this->provision_types = ProvisionType::all();
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
      'provision_name'              =>  ['required', 'string', 'max:50'],
      'provision_trademark_id'      =>  ['required'],
      'provision_type_id'           =>  ['required'],
      'measure_id'                  =>  ['required'],
      'provision_quantity'          =>  ['required', 'numeric', 'between:0.1,9999'],
      'provision_short_description' =>  ['nullable', 'string', 'max:150'],
    ], [], [
      'provision_name'              =>  'nombre del suministro',
      'provision_trademark_id'      =>  'marca',
      'provision_type_id'           =>  'tipo',
      'measure_id'                  =>  'unidad de medida',
      'provision_quantity'          =>  'volumen',
      'provision_short_description' =>  'descripcion',
    ]);

    try {

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
