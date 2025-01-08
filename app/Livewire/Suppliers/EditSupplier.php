<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use Livewire\Component;
use App\Services\Supplier\SupplierService;
use App\Services\User\UserService;
use Illuminate\Validation\Rule;
use App\Rules\CuitCuilRule;
use Illuminate\View\View;

class EditSupplier extends Component
{
  // proveedor a ditar
  public $supplier;

  // proveedor entidad
  public $company_name;
  public $company_cuit;
  public $company_iva;
  public $company_phone;
  public $company_short_desc = "sin descripcion";

  // estado del proveedor
  public $status_is_active;
  public $status_description;
  public $status_date;

  // cambio el estado del proveedor?
  public $status_changed;
  public $status_new_description;

  // proveedor direccion
  public $company_street;
  public $company_number = "s/n";
  public $company_city;
  public $company_postal_code;

  // proveedor credenciales
  public $user_name;
  public $user_email;

  // posibles condiciones de iva
  public $iva_conditions = [];

  /**
   * montar datos
   * @param SupplierService $supplier_service
   * @param int $id id del proveedor
   * @return void
   */
  public function mount(SupplierService $supplier_service, $id): void
  {
    $this->supplier = Supplier::findOrFail($id);
    $this->iva_conditions = $supplier_service->getSuppilerIvaConditions();

    $this->company_name         =  $this->supplier->company_name;
    $this->company_cuit         =  $this->supplier->company_cuit;
    $this->company_iva          =  $this->supplier->iva_condition;
    $this->company_phone        =  $this->supplier->phone_number;
    $this->company_short_desc   =  $this->supplier->short_description;

    // estado actual del proveedor
    $this->status_is_active     =  $this->supplier->status_is_active;
    $this->status_description   =  $this->supplier->status_description;
    $this->status_date          =  $this->supplier->status_date;

    $this->company_street       =  $this->supplier->address->street;
    $this->company_number       =  $this->supplier->address->number;
    $this->company_city         =  $this->supplier->address->city;
    $this->company_postal_code  =  $this->supplier->address->postal_code;

    $this->user_name            =  $this->supplier->user->name;
    $this->user_email           =  $this->supplier->user->email;

    // al cargar los datos, el estado no cambia
    $this->status_changed       =  false;
  }

  /**
   * verificar si cambio el estado del proveedor
   * NOTA: true y false se trabaja en 0 y 1, ojo al comparar con igualdad estricta con true y false.
   * @return void
   */
  public function checkIfStatusChanged()
  {
    // estado actual del proveedor
    $CURRENT_STATUS = $this->supplier->status_is_active;

    if ($CURRENT_STATUS == $this->status_is_active) {
      // lo que tengo es igual a lo elegido, no hay cambio
      $this->status_changed = false;
    } else {
      // lo que tengo es distinto a lo elegido, hay cambio
      $this->status_changed = true;
    }

  }

  /**
   * editar un proveedor
   * @param SupplierService $supplier_service
   * @param UserService $user_service
   * @return void
   */
  public function update(SupplierService $supplier_service, UserService $user_service)
  {
    // validar proveedor, direccion, estado y credenciales de usuario
    $validated = $this->validate([
      'user_email' => [
        'required',
        'max:90',
        Rule::unique('users','email')->ignore($this->supplier->user->id)
      ],
      'company_name' => [
        'required',
        'regex:/^[\p{L}\p{N}\s]+$/u',
        'max:50',
        Rule::unique('suppliers','company_name')->ignore($this->supplier->id)
      ],
      'company_cuit' => [
        'required',
        new CuitCuilRule,
        Rule::unique('suppliers','company_cuit')->ignore($this->supplier->id)
      ],
      'company_iva'   => 'required',
      'company_phone' => [
        'required',
        'size:10',
        Rule::unique('suppliers','phone_number')->ignore($this->supplier->id)
      ],
      'company_short_desc'  => 'nullable|max:150',
      'status_is_active'    => 'required',
      'status_description'  => 'required',
      'status_new_description'  => [Rule::requiredIf($this->status_changed), 'max:150'],
      'company_street'  => 'required|regex:/^[\p{L}\p{N}\s]+$/u|max:45',
      'company_number'  => 'nullable|max:8',
      'company_city'    => 'required|regex:/^[\p{L}\p{N}\s]+$/u|max:45',
      'company_postal_code' => 'required|integer|digits:4|min:1000|max:9999',
    ],[
      'company_name.regex'    => 'El campo :attribute solo permite letras, numeros y espacios',
      'company_street.regex'  => 'El campo :attribute solo permite letras, numeros y espacios',
      'company_city.regex'    => 'El campo :attribute solo permite letras, numeros y espacios'
    ],[
      'user_email'      => 'correo electronico',
      'company_name'  => 'razon social',
      'company_cuit'  => 'cuit',
      'company_iva'   => 'condicion frente al iva',
      'company_phone' => 'telefono de contacto',
      'company_short_desc' => 'descripcion corta',
      'status_description' => 'razón del cambio de estado',
      'company_street'  => 'calle',
      'company_number'  => 'numero de calle',
      'company_city'    => 'ciudad',
      'company_postal_code'  => 'codigo postal'
    ]);

    try {

      // debe tener siempre el rol proveedor
      // el nombre de usuario sera el cuit
      $validated += [
        'user_role' => $supplier_service->getSupplierRolename(),
        'user_name' => $validated['company_cuit'],
      ];

      if ($this->status_changed) {
        // el estado cambia, agregar fecha nueva y descripcion nueva
        $validated += ['status_date' => now()->format('d-m-Y')];
        $validated['status_description'] = $validated['status_new_description'];
      } else {
        // el estado no cambia, mantener fecha y descripcion
        $validated += ['status_date' => $this->supplier->status_date->format('d-m-Y')];
        $validated += ['status_description' => $this->supplier->status_description];
      }

      $updated_supplier_user = $user_service->editInternalUser($this->supplier->user, $validated);
      $updated_supplier = $supplier_service->editSupplier($updated_supplier_user, $this->supplier, $validated);

      $this->reset();

      session()->flash('operation-success', toastSuccessBody('proveedor', 'editado'));
      $this->redirectRoute('suppliers-suppliers-index');

    } catch (\Exception $e) {

      // todo: como elimino registros creados a medias si algo falla?, por ej: usuario o direccion?

      session()->flash('operation-error', 'error: ' . $e->getMessage() . ', contacte al Administrador');
      $this->redirectRoute('suppliers-suppliers-index');

    }
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    return view('livewire.suppliers.edit-supplier');
  }
}
