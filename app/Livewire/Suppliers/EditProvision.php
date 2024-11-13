<?php

namespace App\Livewire\Suppliers;

use App\Models\Provision;
use App\Models\ProvisionTrademark;
use App\Models\ProvisionType;
use App\Models\Measure;
use Livewire\Component;

class EditProvision extends Component
{
  public $provision;
  public $trademarks;
  public $measures;
  public $provision_types;

  // parametros form
  public $provision_name;
  public $provision_quantity;
  public $provision_short_description;
  public $provision_trademark_id;
  public $provision_type_id;
  public $measure_id;
  public $show_pack_form;

  public function mount($id)
  {
    $this->provision = Provision::findOrFail($id);
    $this->trademarks = ProvisionTrademark::all();
    $this->measures = Measure::all();
    $this->provision_types = ProvisionType::all();

    // parametros form
    $this->provision_name               = $this->provision->provision_name;
    $this->provision_quantity           = $this->provision->provision_quantity;
    $this->provision_short_description  = $this->provision->provision_short_description;
    $this->provision_trademark_id       = $this->provision->provision_trademark_id;
    $this->provision_type_id            = $this->provision->provision_type_id;
    $this->measure_id                   = $this->provision->measure_id;

    $this->show_pack_form = false;
  }

  public function save()
  {
    /* dd([
      'name' => $this->provision_name,
      'quantity' => $this->provision_quantity,
      'description' => $this->provision_short_description,
      'trademark' => $this->provision_trademark_id,
      'type' => $this->provision_type_id,
      'measure' => $this->measure_id
    ]); */

    // todo: validar nombre + marca + cantidad unica?

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
      'provision_quantity'          =>  'cantidad',
      'provision_short_description' =>  'descripcion',
    ]);

    //dd($validated);

    try {

      // todo: cuando es seguro editar un suministro, sobre todo su cantidad  o tipo?

      $this->provision->provision_name              = $this->provision_name;
      $this->provision->provision_quantity          = $this->provision_quantity;
      $this->provision->provision_short_description = $this->provision_short_description;
      $this->provision->provision_trademark_id      = $this->provision_trademark_id;
      $this->provision->provision_type_id           = $this->provision_type_id;
      $this->provision->measure_id                  = $this->measure_id;
      $this->provision->save();

      $this->reset();

      session()->flash('operation-success', toastSuccessBody('suministro', 'editado'));
      $this->redirectRoute('suppliers-provisions-index');

    } catch (\Exception $e) {

      session()->flash('operation-error', 'error: ' . $e->getMessage() . ' contacte al Administrador.');
      $this->redirectRoute('suppliers-provisions-index');

    }
  }

  public function render()
  {
    return view('livewire.suppliers.edit-provision');
  }
}
