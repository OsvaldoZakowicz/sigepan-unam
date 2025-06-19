<?php

namespace App\Livewire\Quotations;

use App\Models\Pack;
use Livewire\Component;
use App\Models\Provision;
use App\Models\Quotation;
use Illuminate\View\View;
use App\Models\DatoNegocio;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class RespondQuotation extends Component
{
  // datos de la panaderia
  public $razon_social = '';
  public $cuit = '';
  public $telefono = '';
  public $correo = '';
  public $direccion = '';
  public $inicio_actividades = '';

  // presupuesto
  public $quotation;
  public $provisions;
  public $packs;

  // inputs para suministros, y packs
  public Collection $inputs;
  public $total = 0;

  /**
   * boot de datos constantes
   * @return void
   */
  public function boot(): void
  {
    $this->razon_social = DatoNegocio::obtenerValor('razon_social');
    $this->cuit = DatoNegocio::obtenerValor('cuit');
    $this->telefono = DatoNegocio::obtenerValor('telefono');
    $this->correo = DatoNegocio::obtenerValor('email');
    $this->direccion = DatoNegocio::obtenerValor('domicilio');
    $this->inicio_actividades = DatoNegocio::obtenerValor('inicio_actividades');
  }

  /**
   * inicializar datos
   * @param int $id id del presupuesto a responder
   * @return void
   */
  public function mount($id)
  {
    $this->quotation = Quotation::findOrFail($id);

    if ($this->quotation->period->period_status_id === 3) {
      session()->flash('operation-info', 'El periodo de este presupuesto ha cerrado el ' . formatDateTime($this->quotation->period->period_end_at, 'd-m-Y') . ', ya no puede responderse');
      $this->redirectRoute('quotations-quotations-index');
    }

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
      'item_quantity' => (int) $item->pivot->quantity,
      'item_has_stock'   => true,
      'item_unit_price'  => (float) 0,
      'item_total_price' => (float) 0,
    ]);
  }

  /**
   * al ingresar un precio unitario con formato float valido
   * calcular el subtotal como unitario * cantidad.
   * @param int $key clave donde ocurre el cambio
   * @param $value valor de cambio
   * @return void 
   */
  public function calculateSubtotal(int $key, $value): void
  {
    $item = $this->inputs->get($key);

    if (!is_numeric($value) || (float) $value <= 0) {
      $item['item_unit_price'] = 0.00;
      $item['item_total_price'] = 0.00;
    } else {
      $item['item_unit_price'] = (float) $value;
      $item['item_total_price'] = (float) $item['item_unit_price'] * $item['item_quantity'];
    }

    $this->inputs->put($key, $item);
    $this->calculateTotal();
  }

  /**
   * formato de subtotal y calculo de total
   * cuando el subtotal cambie
   * @param int $key clave donde ocurre el cambio
   * @param $value valor de cambio
   * @return void 
   */
  public function formatSubtotal(int $key, $value): void
  {
    if (!is_numeric($value) || (float) $value <= 0) {

      // formato invalido, recalcular original
      $item = $this->inputs->get($key);
      $item['item_total_price'] = (float) $item['item_unit_price'] * $item['item_quantity'];
    } else {

      // obtener el item y asignar valor
      $item = $this->inputs->get($key);
      $item['item_total_price'] = (float) $value;
    }

    // actualizar item en la coleccion
    $this->inputs->put($key, $item);

    // recalcular el total
    $this->calculateTotal();
  }

  /**
   * calcular total del presupuesto cada vez que
   * algun subtotal se actualice.
   * @return void
   */
  public function calculateTotal(): void
  {
    $this->total = $this->inputs->reduce(function ($acc, $input) {
      return $acc + (float) $input['item_total_price'];
    }, 0);
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
      'inputs.*.item_unit_price'  => ['required_if_accepted:inputs.*.item_has_stock', 'numeric', 'min:0.1', 'regex:/^\d{1,6}(\.\d{1,2})?$/'],
      'inputs.*.item_total_price' => ['required_if_accepted:inputs.*.item_has_stock', 'numeric', 'min:0.1', 'regex:/^\d{1,6}(\.\d{1,2})?$/'],
      'inputs.*.item_id'          => 'nullable',
      'inputs.*.item_type'        => 'nullable',
    ], [
      'inputs.*.item_unit_price.required'              => 'El :attribute es requerido',
      'inputs.*.item_total_price.required'             => 'El :attribute es requerido',
      'inputs.*.item_unit_price.numeric'               => 'El :attribute debe ser un número en formato moneda, ejemplo: 1230.34',
      'inputs.*.item_total_price.regex'                => 'El :attribute puede ser hasta $999999.99',
      'inputs.*.item_unit_price.required_if_accepted'  => 'El :attribute es obligatorio si marco que tiene stock',
      'inputs.*.item_total_price.required_if_accepted' => 'El :attribute es obligatorio si marco que tiene stock',
      'inputs.*.item_unit_price.min'  => 'El :attribute debe ser mayor a cero, si marco que tiene stock',
      'inputs.*.item_total_price.min' => 'El :attribute debe ser mayor a cero, si marco que tiene stock'
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
            $input['item_id'],
            [
              'has_stock'   => $input['item_has_stock'],
              'unit_price'  => $input['item_unit_price'],
              'total_price' => $input['item_total_price'],
            ]
          );
        } else {
          // packs
          $this->quotation->packs()->updateExistingPivot(
            $input['item_id'],
            [
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
