<?php

namespace App\Livewire\Quotations;

use App\Models\Provision;
use App\Models\Pack;
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
  public $packs;

  // inputs para suministros, y packs
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
    $this->packs = $this->quotation->packs;

    // creo un array con un key llamado 'inputs' y un value = []
    $this->fill([
      'inputs' => collect([]),
    ]);

    if ($this->provisions->count() > 0) {
      foreach ($this->provisions as $provision) {
        $this->addInput($provision);
      }
    }

    if ($this->packs->count() > 0) {
      foreach ($this->packs as $pack) {
        $this->addInput($pack);
      }
    }
  }

  /**
   * agregar un suministro al array de inputs
   * * el precio debe ser "vacio": price => ''
   * * el stock se establece a true: has_stock => true
   * @param Provision | Pack $item es un suministro o pack
   * @return void
  */
  public function addInput(Provision|Pack $item): void
  {
    $type = ($item instanceof Provision) ? 'suministro' : 'pack';

    $this->inputs->push([
      'item_type'     => $type,
      'item_id'       => $item->id,
      'item_object'   => $item,
      'item_quantity' => $item->pivot->quantity,
      'item_has_stock'   => true,
      'item_unit_price'  => '',
      'item_total_price' => '',
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
      'inputs.*.item_has_stock'   => 'nullable',
      'inputs.*.item_unit_price'  => ['required_if_accepted:inputs.*.item_has_stock', 'numeric', 'regex:/^\d{1,6}(\.\d{1,2})?$/'],
      'inputs.*.item_total_price' => ['required_if_accepted:inputs.*.item_has_stock', 'numeric', 'regex:/^\d{1,6}(\.\d{1,2})?$/'],
      'inputs.*.item_id'          => 'nullable',
      'inputs.*.item_type'        => 'nullable',
    ], [
      'inputs.*.item_unit_price.required'              => 'El :attribute es requerido',
      'inputs.*.item_total_price.required'             => 'El :attribute es requerido',
      'inputs.*.item_unit_price.numeric'               => 'El :attribute es debe ser un número',
      'inputs.*.item_total_price.regex'                => 'El :attribute puede ser hasta $999999.99',
      'inputs.*.item_unit_price.required_if_accepted'  => 'El :attribute es obligatorio si marco que tiene stock',
      'inputs.*.item_total_price.required_if_accepted' => 'El :attribute es obligatorio si marco que tiene stock'
    ], [
      'inputs.*.item_has_stock'   => 'stock',
      'inputs.*.item_unit_price'  => 'precio unitario',
      'inputs.*.item_total_price' => 'precio total',
    ]);

    try {

      // obtengo cada input, mapeo de tal forma que inputs vacios tengan un precio = 0
      $inputs = Arr::map($validated['inputs'], function ($input) {

        // si no tiene stock o el precio esta vacio
        if (!$input['item_has_stock'] || $input['item_unit_price'] === '' || $input['item_total_price'] === '') {
          $input['item_unit_price'] = 0;
          $input['item_total_price'] = 0;
        }

        return $input;
      });

      // por cada input, obtener el suministro y actualizar el precio para el presupuesto
      foreach ($inputs as $input) {

        // suministros
        if ($input['item_type'] === 'suministro') {

          $this->quotation->provisions()->updateExistingPivot(
            $input['item_id'], [
              'has_stock'   => $input['item_has_stock'],
              'unit_price'  => $input['item_unit_price'],
              'total_price' => $input['item_total_price'],
            ]
          );

        } else {
          // packs
          $this->quotation->packs()->updateExistingPivot(
            $input['item_id'], [
              'has_stock'   => $input['item_has_stock'],
              'unit_price'  => $input['item_unit_price'],
              'total_price' => $input['item_total_price'],
            ]
            );
        }

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
