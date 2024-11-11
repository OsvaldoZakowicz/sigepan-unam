<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use Illuminate\Support\Arr;
use App\Models\ProvisionTrademark;

class CreateTrademark extends Component
{
  public $provision_trademark_name;

  public function save()
  {
    $validated = $this->validate([
      'provision_trademark_name' => [
          'required',
          'string',
          'between:1,50',
          'regex:/^[a-zA-Z\s]{1,50}$/i',
          'unique:provision_trademarks,provision_trademark_name'
      ],
    ],[
      'provision_trademark_name.unique'   =>  'Ya existe una :attribute registrada con el mismo nombre',
      'provision_trademark_name.between'  =>  'La :attribute debe contener entre 1 y 50 caracteres',
      'provision_trademark_name.regex'    =>  'La :attribute solo debe contener letras'
    ],[
      'provision_trademark_name' => 'marca'
    ]);

    try {

      $validated = Arr::add($validated, 'provision_trademark_is_editable', true);
      ProvisionTrademark::create($validated);

      $this->reset();

      session()->flash('operation-success', toastSuccessBody('marca', 'creada'));
      $this->redirectRoute('suppliers-trademarks-index');

    } catch (\Exception $e) {

      session()->flash('operation-error', 'error: ' . $e->getMessage() . ' contacte al Administrador');
      $this->redirectRoute('suppliers-trademarks-index');

    }

  }

  public function render()
  {
    return view('livewire.suppliers.create-trademark');
  }
}
