<?php

namespace App\Livewire\Suppliers;

use App\Models\Measure;
use App\Models\Provision;
use App\Models\ProvisionTrademark;
use App\Models\ProvisionType;
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

  public $show_pack_form;

  public function mount()
  {
    $this->trademarks = ProvisionTrademark::all();
    $this->measures = Measure::all();
    $this->provision_types = ProvisionType::all();
    $this->show_pack_form = false;
  }

  public function togglePackForm()
  {
    $this->show_pack_form = !$this->show_pack_form;
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

    $validated = $this->validate([
      'provision_name'              =>  ['required', 'string', 'max:50'],
      'provision_trademark_id'      =>  ['required'],
      'provision_type_id'           =>  ['required'],
      'measure_id'                  =>  ['required'],
      'provision_quantity'          =>  ['required', 'numeric', 'between:1,9999'],
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

      Provision::create($validated);

      $this->reset();

      session()->flash('operation-success', toastSuccessBody('suministro', 'creado'));
      $this->redirectRoute('suppliers-provisions-index');

    } catch (\Exception $e) {

      session()->flash('operation-error', 'error: ' . $e->getMessage() . ' contacte al Administrador.');
      $this->redirectRoute('suppliers-provisions-index');

    }

  }

  public function render()
  {
    return view('livewire.suppliers.create-provision');
  }
}
