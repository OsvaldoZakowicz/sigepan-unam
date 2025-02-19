<?php

namespace App\Livewire\Stocks;

use App\Models\Tag;
use Illuminate\View\View;
use Livewire\Component;

class CreateTag extends Component
{
  public $tag_name;

  /**
   * guardar etiqueta
   */
  public function save()
  {
    $validated = $this->validate([
      'tag_name' => [
        'required',
        'string',
        'between:1,50',
        'regex:/^[a-zA-Z\s]{1,30}$/i',
        'unique:tags,tag_name'
      ]
    ],[
      'tag_name.unique'   =>  'Ya existe una :attribute registrada con el mismo nombre',
      'tag_name.between'  =>  'La :attribute debe contener entre 1 y 30 caracteres',
      'tag_name.regex'    =>  'La :attribute solo debe contener letras'
    ],[
      'tag_name' => 'etiqueta'
    ]);

    try {
      Tag::create($validated);

      $this->reset('tag_name');

      session()->flash('operation-success', toastSuccessBody('etiqueta', 'creada'));
      $this->redirectRoute('stocks-tags-index');

    } catch (\Exception $e) {
      session()->flash('operation-error', 'error: ' . $e->getMessage() . ' contacte al Administrador');
      $this->redirectRoute('stocks-tags-index');
    }
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    return view('livewire.stocks.create-tag');
  }
}
