<?php

namespace App\Livewire\Suppliers;

use App\Models\ProvisionTrademark;
use Illuminate\View\View;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Livewire\Component;

class EditTrademark extends Component
{

  public $trademark;

  public $provision_trademark_name;

  /**
   * montar datos
   * @param
   * @return
   */
  public function mount(int $id)
  {
    $this->trademark = ProvisionTrademark::findOrFail($id);

    if (!$this->trademark->provision_trademark_is_editable) {

      session()->flash('operation-info', 'No se puede editar la marca, la misma es propia del sistema');
      $this->redirectRoute('suppliers-trademarks-index');

      return;
    }

    if ($this->trademark->provisions->count() > 0) {

      session()->flash('operation-info', 'No se puede editar la marca, la misma se usa en suministros');
      $this->redirectRoute('suppliers-trademarks-index');

      return;
    }

    $this->provision_trademark_name = $this->trademark->provision_trademark_name;
  }

  /**
   * guardar marca
  */
  public function save()
  {
    $this->validate([
      'provision_trademark_name' => [
        'required',
        'string',
        'between:1,50',
        'regex:/^[a-zA-Z\s]{1,50}$/i',
        Rule::unique('provision_trademarks', 'provision_trademark_name')->ignore($this->trademark->id)
      ],
    ],[
      'provision_trademark_name.unique'   =>  'Ya existe una :attribute registrada con el mismo nombre',
      'provision_trademark_name.between'  =>  'La :attribute debe contener entre 1 y 50 caracteres',
      'provision_trademark_name.regex'    =>  'La :attribute solo debe contener letras'
    ],[
      'provision_trademark_name' => 'marca'
    ]);

    try {

      $this->trademark->provision_trademark_name = $this->provision_trademark_name;
      $this->trademark->save();

      $this->reset();

      session()->flash('operation-success', toastSuccessBody('marca', 'creada'));
      $this->redirectRoute('suppliers-trademarks-index');
    } catch (\Exception $e) {

      session()->flash('operation-error', 'error: ' . $e->getMessage() . ' contacte al Administrador');
      $this->redirectRoute('suppliers-trademarks-index');
    }
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    return view('livewire.suppliers.edit-trademark');
  }
}
