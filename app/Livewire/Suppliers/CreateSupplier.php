<?php

namespace App\Livewire\Suppliers;

use App\Mail\SupplierRegistered;
use Livewire\Component;
use App\Services\Supplier\SupplierService;
use App\Services\User\UserService;
use App\Rules\CuitCuilRule;
use Illuminate\Support\Facades\Mail;

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

    $validated = $this->validate([
      'user_email'      => 'required|unique:users,email|max:90',
      'company_name'  => 'required|regex:/^[\p{L}\p{N}\s]+$/u|unique:suppliers,company_name|max:50',
      'company_cuit'  => ['required', 'regex:/^[0-9]{11}$/u', 'unique:suppliers,company_cuit', new CuitCuilRule],
      'company_iva'   => 'required',
      'company_phone' => 'required|unique:suppliers,phone_number|size:10',
      'company_short_desc' => 'nullable|max:150',
      'company_street'  => 'required|regex:/^[\p{L}\p{N}\s]+$/u|max:45',
      'company_number'  => 'nullable|max:8',
      'company_city'    => 'required|regex:/^[\p{L}\p{N}\s]+$/u|max:45',
      'company_postal_code' => 'required|integer|digits:4|min:1000|max:9999',
    ],[
      'company_name.regex'    => 'El campo :attribute solo permite letras, numeros y espacios',
      'company_cuit.regex'    => 'El campo :attribute debe ser un numero de 11 digitos',
      'company_street.regex'  => 'El campo :attribute solo permite letras, numeros y espacios',
      'company_city.regex'    => 'El campo :attribute solo permite letras, numeros y espacios',
    ],[
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

      $validated += [
        'user_role' => $supplier_service->getSupplierRolename(),
        'user_name' => $validated['company_cuit'],
        'user_password' => randomPassword(),
        'company_status_is_active' => true,
        'company_status_description' => "proveedor activo",
        'company_status_date' => now(),
      ];

      $supplier_user = $user_service->createInternalUser($validated);
      $supplier = $supplier_service->createSupplier($supplier_user, $validated);

      Mail::to($supplier_user->email)->send(new SupplierRegistered($supplier_user, $validated['user_password'], $supplier));

      $this->reset();

      session()->flash('operation-success', toastSuccessBody('proveedor', 'creado'));
      $this->redirectRoute('suppliers-suppliers-index');

    } catch (\Exception $e) {

      // todo: como elimino registros creados a medias si algo falla?, por ej: usuario o direccion?

      session()->flash('operation-error', 'error: ' . $e->getMessage() . ', contacte al Administrador');
      $this->redirectRoute('suppliers-suppliers-index');

    }
  }

  public function render()
  {
      return view('livewire.suppliers.create-supplier');
  }
}
