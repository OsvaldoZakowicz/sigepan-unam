<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use Livewire\Component;
use App\Services\Supplier\SupplierService;
use App\Services\User\UserService;
use Illuminate\Validation\Rule;
use App\Rules\CuitCuilRule;

class EditSupplier extends Component
{
  // proveedor a ditar
  public $supplier;

  // proveedor entidad
  public $company_name;
  public $company_cuit;
  public $company_iva;
  public $company_phone;
  // debido al campo nullable, inicializar
  public $company_short_desc = "sin descripcion";

  // proveedor direccion
  public $company_street;
  // debido al campo nullable, inicializar
  public $company_number = "s/n";
  public $company_city;
  public $company_postal_code;

  // proveedor credenciales
  public $user_name;
  public $user_email;

  // posibles condiciones de iva
  public $iva_condition_params = [];

  //* montar datos
  public function mount(SupplierService $supplier_service, $id)
  {
    $this->supplier = Supplier::findOrFail($id);
    $this->iva_condition_params = $supplier_service->getSuppilerIvaConditions();

    $this->company_name        = $this->supplier->company_name;
    $this->company_cuit        = $this->supplier->company_cuit;
    $this->company_iva         = $this->supplier->iva_condition;
    $this->company_phone       = $this->supplier->phone_number;
    $this->company_short_desc  = $this->supplier->short_description;
    $this->company_street      = $this->supplier->address->street;
    $this->company_number      = $this->supplier->address->number;
    $this->company_city        = $this->supplier->address->city;
    $this->company_postal_code = $this->supplier->address->postal_code;
    $this->user_name           = $this->supplier->user->name;
    $this->user_email          = $this->supplier->user->email;
  }

  public function update(SupplierService $supplier_service, UserService $user_service)
  {
    // validar proveedor, direccion y credenciales de usuario
    $validated = $this->validate([
      'user_name'       => 'required|regex:/^[\p{L}\p{N}\s]+$/u|max:50',
      'user_email'      => ['required','max:90', Rule::unique('users','email')->ignore($this->supplier->user->id)],
      'company_name'  => ['required','regex:/^[\p{L}\p{N}\s]+$/u','max:50',Rule::unique('suppliers','company_name')->ignore($this->supplier->id)],
      'company_cuit'  => ['required', new CuitCuilRule, Rule::unique('suppliers','company_cuit')->ignore($this->supplier->id)],
      'company_iva'   => 'required',
      'company_phone' => ['required','size:10',Rule::unique('suppliers','phone_number')->ignore($this->supplier->id)],
      'company_short_desc' => 'nullable|max:150',
      'company_street'  => 'required|regex:/^[\p{L}\p{N}\s]+$/u|max:45',
      'company_number'  => 'nullable|max:8',
      'company_city'    => 'required|regex:/^[\p{L}\p{N}\s]+$/u|max:45',
      'company_postal_code' => 'required|integer|digits:4|min:1000|max:9999',
    ],[
      'user_name.regex'       => 'El campo :attribute solo permite letras, numeros y espacios',
      'company_name.regex'    => 'El campo :attribute solo permite letras, numeros y espacios',
      'company_street.regex'  => 'El campo :attribute solo permite letras, numeros y espacios',
      'company_city.regex'    => 'El campo :attribute solo permite letras, numeros y espacios',
    ],[
      'user_name'       => 'nombre de usuario',
      'user_email'      => 'correo electronico',
      'company_name'  => 'razon social',
      'company_cuit'  => 'cuit',
      'company_iva'   => 'condicion frente al iva',
      'company_phone' => 'telefono de contacto',
      'company_short_desc' => 'descripcion corta',
      'company_street'  => 'calle',
      'company_number'  => 'numero de calle',
      'company_city'    => 'ciudad',
      'company_postal_code'  => 'codigo postal'
    ]);

    try {

      // debe tener siempre el rol proveedor
      $validated += ['user_role' => $supplier_service->getSupplierRolename()];

      $updated_supplier_user = $user_service->editInternalUser($this->supplier->user, $validated);
      $updated_supplier = $supplier_service->editSupplier($updated_supplier_user, $this->supplier, $validated);

      $this->reset();

      session()->flash('operation-success', toastSuccessBody('proveedor', 'editado'));
      $this->redirectRoute('suppliers-suppliers-index');

    } catch (\Exception $e) {

      // todo: como elimino registros creados a medias, por ej: usuario o direccion?

      session()->flash('operation-error', 'error: ' . $e->getMessage() . ', contacte al Administrador');
      $this->redirectRoute('suppliers-suppliers-index');

    }
  }

  public function render()
  {
    return view('livewire.suppliers.edit-supplier');
  }
}
