<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use App\Services\Supplier\SupplierService;
use App\Services\User\UserService;
use App\Rules\CuitCuilRule;

class CreateSupplier extends Component
{
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
  public $user_password;
  public $user_passw_test;

  // posibles condiciones de iva
  public $iva_condition_params = [];

  //* montar datos
  public function mount(SupplierService $supplier_service)
  {
    $this->iva_condition_params = $supplier_service->getSuppilerIvaConditions();
  }

  //* crear un proveedor
  public function save(SupplierService $supplier_service, UserService $user_service)
  {

    // validar proveedor, direccion y credenciales de usuario
    // todo: validacion corecta del cuit, formato adecuado.
    $validated = $this->validate([
      'user_name'       => 'required|regex:/^[\p{L}\p{N}\s]+$/u|max:50',
      'user_email'      => 'required|unique:users,email|max:90',
      'user_password'   => 'required|min:8|max:20',
      'user_passw_test' => 'required|min:8|max:20|same:user_password',
      'company_name'  => 'required|regex:/^[\p{L}\p{N}\s]+$/u|unique:suppliers,company_name|max:50',
      'company_cuit'  => ['required', 'unique:suppliers,company_cuit', new CuitCuilRule],
      'company_iva'   => 'required',
      'company_phone' => 'required|unique:suppliers,phone_number|size:10',
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
      'user_password'      => 'contraseña',
      'user_passw_test' => 'repetir contraseña',
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

      // necesito el rol proveedor
      $validated += ['user_role' => $supplier_service->getSupplierRolename()];

      $supplier_user = $user_service->createInternalUser($validated);
      $supplier = $supplier_service->createSupplier($supplier_user, $validated);

      $this->reset();

      session()->flash('operation-success', toastSuccessBody('proveedor', 'creado'));
      $this->redirectRoute('suppliers-suppliers-index');

    } catch (\Exception $e) {

      session()->flash('operation-error', 'error: ' . $e->getMessage() . ', contacte al Administrador');
      $this->redirectRoute('suppliers-suppliers-index');

    }
  }

  public function render()
  {
      return view('livewire.suppliers.create-supplier');
  }
}
