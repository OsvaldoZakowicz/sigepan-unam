<?php

namespace App\Livewire\Quotations;

use App\Models\Provision;
use App\Models\Quotation;
use Illuminate\Support\Collection;
use Livewire\Component;

class RespondQuotation extends Component
{
  // presupuesto
  public $quotation;
  public $provisions;

  // inputs precio
  public Collection $inputs;

  // reglas de validacion, solo necesito traer el id junto al precio
  protected $rules = [
    'inputs.*.price'        => ['required', 'numeric', 'regex:/^\d{1,6}(\.\d{1,2})?$/'],
    'inputs.*.provision_id' => 'nullable',
  ];

  // mensajes de validacion
  protected $messages = [
    'inputs.*.price.required' => 'El precio es requerido',
    'inputs.*.price.numeric' => 'El precio es debe ser un número',
    'inputs.*.price.regex' => 'El precio puede ser hasta $999999.99',
  ];

  // preparar inputs
  public function mount($id)
  {
    $this->quotation = Quotation::findOrFail($id);
    $this->provisions = $this->quotation->provisions;

    // creo un array con un key llamado 'inputs' y un value = []
    $this->fill([
      'inputs' => collect([]),
    ]);

    // completo el array de inputs
    foreach ($this->provisions as $provision) {
      $this->addInput($provision);
    }
  }

  //* agrego un input al formulario
  public function addInput(Provision $provision)
  {
    $this->inputs->push([
      'provision_id' => $provision->id,
      'provision_name' => $provision->provision_name,
      'provision_trademark' => $provision->trademark->provision_trademark_name,
      'provision_quantity' => $provision->provision_quantity,
      'provision_quantity_abrv' => $provision->measure->measure_abrv,
      'price' => ''
    ]);
  }

  //* guardar presupuesto
  public function submit()
  {
    // aplico las reglas y mensajes de validacion
    $validated = $this->validate();

    // obtengo cada input
    $inputs = $validated['inputs'];

    try {

      // por cada input, obtener el suministro y actualizar el precio
      // para el presupuesto
      foreach ($inputs as $input) {
        $this->quotation->provisions()->updateExistingPivot($input['provision_id'], [
          'price'     => $input['price'],
          'has_stock' => true
        ]);
      }

      // marcar presupuesto como completado
      $this->quotation->is_completed = true;
      $this->quotation->save();

      $this->reset();

      session()->flash('operation-success', toastSuccessBody('presupuesto', 'completado y enviado'));
      $this->redirectRoute('quotations-quotations-index');

    } catch (\Exception $e) {

      session()->flash('operation-error', 'error: ' . $e->getMessage() . ', contacte al Administrador');
      $this->redirectRoute('quotations-quotations-index');

    }

  }

  public function render()
  {
    return view('livewire.quotations.respond-quotation');
  }
}
