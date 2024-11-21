<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use App\Models\Provision;
use App\Models\Supplier;
use Livewire\Attributes\On;

//* componente de formulario individual para actualizar un precio
class InputPriceForm extends Component
{
  public $supplier;
  public $provision;
  public $provision_array_key;

  // campos del formulario
  public $provision_price;

  // error de validacion
  public $validation_error;
  public $validation_error_message;

  // input de edicion
  public $is_editing;

  // montar datos
  public function mount($provision_id, $supplier_id, $provision_array_key, $is_editing = false)
  {
    $this->provision = Provision::findOrFail($provision_id);
    $this->supplier = Supplier::findOrFail($supplier_id);
    $this->provision_array_key = $provision_array_key;
    $this->validation_error = false;
    $this->is_editing = $is_editing;

    // si se trata de una edicion, completar el input con el precio
    if ($this->is_editing) {
      // proveedor->suministros->donde(id suministro BD = id suministro elegido)->primero->tabla_intermedia->precios
      $this->provision_price = $this->supplier->provisions()
        ->where('provision_id', $provision_id)->first()->pivot->price;
    }
  }

  // validacion, numero decimal, positivo, no nulo
  public function isValidPositiveDecimal(mixed $value): bool
  {
    if (!is_numeric($value)) {
      return false;
    }

    // retorno true cuando cumple ambas condiciones
    return $value !== null && $value > 0;
  }

  // error de validacion
  // no es un numero positivo o es nulo
  function positiveDecimalError()
  {
    $this->validation_error = true;
    $this->validation_error_message = 'el precio debe ser un numero decimal positivo no nulo';
  }

  // validacion en la cantidad de digitos enteros
  function isValidDecimalWithMaxIntDigits(mixed $value, int $max_int_digits = 6, int $max_decimal_digits = 2): bool
  {
    if (!is_numeric($value)) {
        return false;
    }

    // divide el valor, casteado a string, en dos en el punto
    // el punto separa un numero en formato float: entero.decimal
    $parts = explode('.', (string) $value);
    // cuento la cantidad de digitos enteros
    $int_digits = strlen($parts[0]);
    // cuento la cantidad de digitos decimales, si hay, o asigno 0
    $decimal_digits = isset($parts[1]) ? strlen($parts[1]) : 0;

    // retorno true si la parte entera y la decimal son menores y/o iguales a lo permitido
    return $int_digits <= $max_int_digits && $decimal_digits <= $max_decimal_digits;
  }

  // error de validacion
  // no cumple con la longitud y cantidad de digitos aceptados
  function maxDigitsError(string $max_format = '999999.99')
  {
    $this->validation_error = true;
    $this->validation_error_message = 'el precio debe cumplir con un formato máximo de: ' . $max_format;
  }

  // limpiar error
  function resetError()
  {
    $this->validation_error = false;
    $this->validation_error_message = '';
  }

  //* al recibir notificacion de guardado
  // la notificacion proviene del componente livewire AddToPriceList
  #[On('save-prices')]
  public function savePrice()
  {

    // el precio debe ser un numero decimal positivo no nulo
    if (!$this->isValidPositiveDecimal($this->provision_price))
    {
      $this->positiveDecimalError();
      return;
    }

    $this->resetError();

    // el precio debe cumplir con la longitud maxima aceptada
    if (!$this->isValidDecimalWithMaxIntDigits($this->provision_price))
    {
      $this->maxDigitsError();
      return;
    }

    $this->resetError();


    try {

      if ($this->is_editing) {

        // editar suministro con precio
        $this->supplier->provisions()
          ->updateExistingPivot($this->provision->id, ['price' => $this->provision_price]);

        // al guardar exitosamente, quitar suministro de la lista
        $this->dispatch('edit-success', id: $this->provision_array_key)->to(EditOnPriceList::class);

      } else {

        // guardar suministro con precio
        $this->supplier->provisions()
          ->attach($this->provision->id, ['price' => $this->provision_price]);

        // al guardar exitosamente, quitar suministro de la lista
        $this->dispatch('save-success', id: $this->provision_array_key)->to(AddToPriceList::class);
      }



    } catch (\Exception $e) {

      // manejar errores
      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'error',
        'title_toast' => 'error',
        'descr_toast' => 'error: ' . $e->getMessage() . ' contacte al Administrador',
      ]);

    }
  }

  //* al seleccionar un suministro para quitarlo de la lista
  // notifica al componente livewire AddToPriceList
  // $id del suministro a quitar, posicion del array
  public function removeProvision()
  {
    $this->dispatch('remove-provision', id: $this->provision_array_key);
  }

  public function render()
  {
    return view('livewire.suppliers.input-price-form');
  }
}
