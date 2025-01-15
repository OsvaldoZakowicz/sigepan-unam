<?php

namespace App\Livewire\Quotations;

use App\Models\Provision;
use App\Models\Quotation;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Livewire\Component;

class RespondQuotation extends Component
{
  // presupuesto
  public $quotation;
  public $provisions;

  // inputs para suministro y precio
  public Collection $inputs;

  /**
   * inicializar datos
   * @param int $id id del presupuesto a responder
   * @return void
  */
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

  /**
   * agregar un suministro al array de inputs
   * * el precio debe ser "vacio": price => ''
   * * el stock se establece a true: has_stock => true
   * @param Provision $provision suministro
   * @return void
  */
  public function addInput(Provision $provision): void
  {
    $this->inputs->push([
      'provision_id' => $provision->id,
      'provision_name' => $provision->provision_name,
      'provision_trademark' => $provision->trademark->provision_trademark_name,
      'provision_quantity' => $provision->provision_quantity,
      'provision_quantity_abrv' => $provision->measure->measure_abrv,
      'price' => '',
      'has_stock' => true
    ]);
  }

  /**
   * guardar presupuesto
   * se trata de la respuesta con el precio para cada input suministro generado y montado.
   * @return void
  */
  public function submit(): void
  {
    // aplico las reglas y mensajes de validacion
    $validated = $this->validate([
      'inputs.*.has_stock'    => 'nullable',
      'inputs.*.price'        => ['required_if_accepted:inputs.*.has_stock', 'numeric', 'regex:/^\d{1,6}(\.\d{1,2})?$/'],
      'inputs.*.provision_id' => 'nullable',
    ], [
      'inputs.*.price.required'             => 'El precio es requerido',
      'inputs.*.price.numeric'              => 'El precio es debe ser un número',
      'inputs.*.price.regex'                => 'El precio puede ser hasta $999999.99',
      'inputs.*.price.required_if_accepted' => 'El precio es obligatorio si marco que tiene stock'
    ]);

    try {

      // obtengo cada input, mapeo de tal forma que inputs vacios tengan un precio = 0
      $inputs = Arr::map($validated['inputs'], function ($input) {
        // $input = [..., 'price' => '', 'has_stock' => true|false]
        // si no tiene stock o el precio esta vacio
        if (!$input['has_stock'] || $input['price'] === '') {
          $input['price'] = 0;
        }
        return $input;
      });

      // por cada input, obtener el suministro y actualizar el precio para el presupuesto
      foreach ($inputs as $input) {
        $this->quotation->provisions()->updateExistingPivot(
          $input['provision_id'], ['price' => $input['price'], 'has_stock' => $input['has_stock']]
        );
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

  /**
   * renderizar vista
   * @return view
  */
  public function render(): View
  {
    return view('livewire.quotations.respond-quotation');
  }
}
