<?php

namespace App\Livewire\Stocks;

use App\Models\Tag;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

class EditTag extends Component
{
  public Tag $tag;
  public $tag_name;

  /**
   * Inicializa el componente con un Tag existente
   * @param int $id ID del Tag a editar
   */
  public function mount($id)
  {
    $this->tag = Tag::findOrFail($id);

    if ($this->tag->products()->exists()) {

      session()->flash('operation-info', 'No se puede editar una etiqueta que está asociada a productos');
      $this->redirectRoute('stocks-tags-index');
    }

    $this->tag_name = $this->tag->tag_name;
  }

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
        Rule::unique('tags', 'tag_name')->ignore($this->tag->id)
      ]
    ],[
      'tag_name.unique'   =>  'Ya existe una :attribute registrada con el mismo nombre',
      'tag_name.between'  =>  'La :attribute debe contener entre 1 y 30 caracteres',
      'tag_name.regex'    =>  'La :attribute solo debe contener letras'
    ],[
      'tag_name' => 'etiqueta'
    ]);

    try {

      $this->tag->tag_name = $validated['tag_name'];
      $this->tag->save();

      $this->reset('tag_name');

      session()->flash('operation-success', toastSuccessBody('etiqueta', 'actualizada'));
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
    return view('livewire.stocks.edit-tag');
  }
}
