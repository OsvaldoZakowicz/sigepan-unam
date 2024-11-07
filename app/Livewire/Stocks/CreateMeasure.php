<?php

namespace App\Livewire\Stocks;

use App\Models\Measure;
use Livewire\Component;
use Illuminate\Support\Arr;

class CreateMeasure extends Component
{
  public $measure_name;
  public $measure_abrv;
  public $measure_base;
  public $measure_short_description = 'sin descripcion';
  public $measure_is_editable = true;

  public function save()
  {
    $validated = $this->validate([
      'measure_name'  =>  ['required', 'string', 'regex:/^([a-zA-Z][a-zA-Z ]{3,48}[a-zA-Z])$/m', 'unique:measures,measure_name'],
      'measure_abrv'  =>  ['required', 'string', 'regex:/^([a-zA-Z]){1,4}$/m', 'unique:measures,measure_abrv'],
      'measure_base'  =>  ['required', 'numeric', 'between:1,9999'],
      'measure_short_description' =>  ['nullable', 'string', 'regex:/^([a-zA-Z][a-zA-Z0-9, ]{3,149}[a-zA-Z])$/m', 'between:5,150'],
    ], [
      'required'  =>  'El :attribute es obligatorio',
      'string'    =>  'El :attribute debe ser un texto',
      'measure_name.regex'    =>  'El :attribute solo debe contener letras y espacios entre palabras, y de 5 a 50 caracteres',
      'measure_name.unique'   =>  'Ya existe un :attribute con el mismo nombre',
      'measure_abrv.regex'    =>  'La :attribute solo debe contener letras sin espacios, y de 1 a 4 caracteres',
      'measure_abrv.unique'   =>  'Ya existe un :attribute con el mismo nombre',
      'measure_base.numeric'  =>  'El :attribute debe ser un numero',
      'measure_base.between'  =>  'El :attribute debe tener una longitud de 0 a 9999 digitos',
      'measure_short_description.regex'   => 'El :attribute solo debe contener letras, numeros y espacios entre palabras, y de 5 a 150 caracteres',
    ], [
      'measure_name'  =>  'nombre de unidad',
      'measure_abrv'  =>  'abreviatura de unidad',
      'measure_base'  =>  'cantidad base',
      'measure_short_description' =>  'descripcion corta',
    ]);

    try {

      $measure_data = Arr::add($validated, 'measure_is_editable', $this->measure_is_editable);
      Measure::create($measure_data);

      $this->reset();

      session()->flash('operation-success', toastSuccessBody('unidad de medida', 'creada'));
      $this->redirectRoute('stocks-measures-index');

    } catch (\Exception $e) {

      session()->flash('operation-error', 'error: ' . $e->getMessage() . ', contacte al Administrador');
      $this->redirectRoute('stocks-measures-index');

    }
  }

  public function render()
  {
    return view('livewire.stocks.create-measure');
  }
}
