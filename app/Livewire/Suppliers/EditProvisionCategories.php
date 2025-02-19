<?php

namespace App\Livewire\Suppliers;

use App\Models\ProvisionCategory;
use App\Models\Measure;
use App\Models\ProvisionType;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

class EditProvisionCategories extends Component
{

  public ProvisionCategory $category;

  public string $provision_category_name;
  public int $measure_id;
  public int $provision_type_id;

  public Collection $measures;
  public Collection $types;

  /**
   * boot de datos constantes
   * @return void
   */
  public function boot(): void
  {
    $this->measures = Measure::all();
    $this->types = ProvisionType::all();
  }

  /**
   * Mount the component with a specific category ID
   * @param int $id
   * @return void
   */
  public function mount(int $id): void
  {
    $this->category = ProvisionCategory::findOrFail($id);

    if (!$this->category->provision_category_is_editable) {

      session()->flash('operation-info', 'No se puede editar la categoria, la misma es propia del sistema');
      $this->redirectRoute('suppliers-categories-index');

      return;
    }

    if ($this->category->provisions->count() > 0) {

      session()->flash('operation-info', 'No se puede editar la categoria, la misma se usa en suministros');
      $this->redirectRoute('suppliers-categories-index');

      return;
    }

    $this->provision_category_name = $this->category->provision_category_name;
    $this->measure_id = $this->category->measure_id;
    $this->provision_type_id = $this->category->provision_type_id;
  }

  /**
   * guardar categoría
   */
  public function save()
  {
    $validated = $this->validate(
      [
        'provision_category_name' => [
          'required',
          'string',
          'between:1,50',
          'regex:/^[a-zA-Z0-9\s]{1,50}$/i',
          Rule::unique('provision_categories', 'provision_category_name')->ignore($this->category->id)
        ],
        'measure_id' => ['required'],
        'provision_type_id' => ['required']
      ], [
        'provision_category_name.required' => 'El nombre de la categoría es requerido',
        'provision_category_name.string' => 'El nombre debe ser texto',
        'provision_category_name.between' => 'El nombre debe tener entre 1 y 50 caracteres',
        'provision_category_name.regex' => 'El nombre solo puede contener letras, numeros y espacios',
        'provision_category_name.unique' => 'El nombre de la categoría ya existe',
        'measure_id.required' => 'La medida es requerida',
        'provision_type_id.required' => 'El tipo de provisión es requerido'
      ]
    );

    try {

      $this->category->provision_category_name = $validated['provision_category_name'];
      $this->category->measure_id = $validated['measure_id'];
      $this->category->provision_type_id = $validated['provision_type_id'];
      $this->category->save();

      $this->reset(['provision_category_name', 'measure_id', 'provision_type_id']);

      session()->flash('operation-success', toastSuccessBody('categoria', 'editada'));
      $this->redirectRoute('suppliers-categories-index');

    } catch (\Exception $e) {

      session()->flash('operation-error', 'error: ' . $e->getMessage() . ' contacte al Administrador');
      $this->redirectRoute('suppliers-categories-index');

    }
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    return view('livewire.suppliers.edit-provision-categories');
  }
}
