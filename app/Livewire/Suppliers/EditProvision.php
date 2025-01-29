<?php

namespace App\Livewire\Suppliers;

use App\Models\Provision;
use App\Models\ProvisionTrademark;
use App\Models\ProvisionType;
use App\Models\Measure;
use App\Models\Pack;
use Illuminate\View\View;
use Livewire\Component;

class EditProvision extends Component
{
  public $provision;
  public $trademarks;
  public $measures;
  public $provision_types;

  // suministro
  public $provision_name;
  public $provision_quantity;
  public $provision_short_description;
  public $provision_trademark_id;
  public $provision_type_id;
  public $measure_id;
  public $show_pack_form;

  // pack
  public $pack_units;
  public $packs;
  public $new_packs;
  public $packs_to_delete;

  // puedo editar el suministro?
  public $can_edit;

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
   * @param int $id del suministro
   * @return void
   *
  */
  public function mount(int $id)
  {
    // suministro
    $this->provision = Provision::findOrFail($id);

    // puedo editar el suministro
    if ($this->provision->suppliers->count() > 0) {
      $this->can_edit = false;
    } else {
      $this->can_edit = true;
    }

    // datos a editar
    $this->provision_name               = $this->provision->provision_name;
    $this->provision_quantity           = $this->provision->provision_quantity;
    $this->provision_short_description  = $this->provision->provision_short_description;
    $this->provision_trademark_id       = $this->provision->provision_trademark_id;
    $this->provision_type_id            = $this->provision->provision_type_id;
    $this->measure_id                   = $this->provision->measure_id;

    // packs creados del suministro
    $this->packs = collect($this->provision->packs);

    // packs a crear
    $this->new_packs = collect();

    // packs a eliminar
    $this->packs_to_delete = collect();
  }

  /**
   * agregar packs a la lista
   * agregar packs de unidades a la lista de packs a crear
   * siempre que el pack no este creado o no haya sido elegido.
   * @return void
  */
  public function addPackUnits(): void
  {

    if ($this->pack_units == 0 || $this->pack_units == null) {
      return;
    }

    // comprobar que el pack no este en la lista de packs creados ni en los nuevos
    $already_created = $this->packs->contains('pack_units', $this->pack_units);
    $already_selected = $this->new_packs->contains($this->pack_units);
    $already_deleted = $this->packs_to_delete->contains('pack_units', $this->pack_units);

    if ($already_created) {
      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'ya existe un pack de ' . $this->pack_units . ' unidades para este suministo'
      ]);

      return;
    }

    if ($already_selected) {
      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'ya eligió crear un pack de ' . $this->pack_units . ' unidades para este suministo'
      ]);

      return;
    }

    if ($already_deleted) {
      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'ya eligió eliminar el existente pack de ' . $this->pack_units . ' unidades para este suministo, para conservarlo o reestablecerlo debe cancelar la eliminacion'
      ]);

      return;
    }

    // packs nuevos a crear
    $this->new_packs->prepend($this->pack_units);
    $this->reset('pack_units');
  }

  /**
   * remover packs de la lista de nuevos packs a crear
   * remueve packs seleccionados para su creacion.
   * @param int $index indice del elemento a remover
   * @return void
  */
  public function removePackUnits($index)
  {
    $this->new_packs->forget($index);
  }

  /**
   * marcar para eliminar pack de unidades
   * mueve un pack de la lista de creados a la lista de pendientes de eliminacion
   * @param Pack $pack id del pack a eliminar
   * @param int $index indice del pack en la lista
   * @return void
  */
  public function deletePackUnits(Pack $pack, $index): void
  {
    // si el pack esta asignado a uno o mas proveedores no se puede eliminar
    if ($pack->suppliers->count() !== 0) {

      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'No se puede eliminar el pack de ' . $pack->pack_units . ' unidades, está asociado a listas de precios de proveedores',
      ]);

      return;
    }

    $this->packs->forget($index);
    $this->packs_to_delete->prepend($pack);
  }

  /**
   * cancelar eliminacion de pack
   * mueve un pack de la lista de eliminados a la lista de creados
   * @param Pack $pack cuya eliminacion se cancela
   * @param int $index indice del pack en la lista
   * @return void
  */
  public function restorePackUnits(Pack $pack, $index): void
  {
    $this->packs_to_delete->forget($index);
    $this->packs->prepend($pack);
  }

  /**
   * guardar suministro
   * @return void
  */
  public function save()
  {
    $this->validate([
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
      'provision_quantity'          =>  'cantidad',
      'provision_short_description' =>  'descripcion',
    ]);

    try {

      // packs a eliminar
      if ($this->packs_to_delete->count() > 0) {
        $this->packs_to_delete->each(function ($pack) {
          $pack->delete();
        });
      }

      // si el volumen del suministro, o su nombre cambio, se deben modificar los packs existentes
      if ($this->provision_quantity !== $this->provision->provision_quantity || $this->provision_name !== $this->provision->provision_name) {
        $this->packs->each(function ($pack) {
          $pack->pack_quantity = $this->provision_quantity * $pack->pack_units;
          $pack->pack_name = 'pack de ' . $this->provision_name . ' x ' . $pack->pack_units;
          $pack->save();
        });
      }

      // actualizar el suministro
      $this->provision->provision_name              = $this->provision_name;
      $this->provision->provision_quantity          = $this->provision_quantity;
      $this->provision->provision_short_description = $this->provision_short_description;
      $this->provision->provision_trademark_id      = $this->provision_trademark_id;
      $this->provision->provision_type_id           = $this->provision_type_id;
      $this->provision->measure_id                  = $this->measure_id;
      $this->provision->save();

      // packs a crear
      $this->new_packs->each(function ($pack_units) {
        $this->provision->packs()->create([
          'pack_name'     => 'pack de ' . $this->provision->provision_name . ' x ' . $pack_units,
          'pack_units'    => $pack_units,
          'pack_quantity' => $this->provision->provision_quantity * $pack_units
        ]);
      });

      $this->reset();

      session()->flash('operation-success', toastSuccessBody('suministro', 'editado'));
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
    return view('livewire.suppliers.edit-provision');
  }
}
