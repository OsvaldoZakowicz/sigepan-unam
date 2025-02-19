<?php

namespace App\Livewire\Suppliers;

use App\Models\Measure;
use App\Models\ProvisionCategory;
use App\Models\ProvisionType;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class CreateProvisionCategories extends Component
{

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
          'unique:provision_categories,provision_category_name'
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

      $validated['provision_category_is_editable'] = true;

      ProvisionCategory::create($validated);

      $this->reset(['provision_category_name', 'measure_id', 'provision_type_id']);

      session()->flash('operation-success', toastSuccessBody('categoria', 'creada'));
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
    return view('livewire.suppliers.create-provision-categories');
  }
}
