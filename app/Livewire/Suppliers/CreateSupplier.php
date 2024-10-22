<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use App\Models\Address;
use App\Models\User;
use App\Models\Supplier;

class CreateSupplier extends Component
{
  // proveedor entidad
  public $company_name;
  public $company_cuit;
  public $company_iva;
  public $company_phone;
  public $company_short_desc = "sin descripcion"; // debido al campo nullable, inicializar

  // proveedor direccion
  public $company_street;
  public $company_number = "s/n"; // debido al campo nullable, inicializar
  public $company_city;
  public $company_postal_code;

  // proveedor credenciales
  public $user_name;
  public $user_email;
  public $user_passw;
  public $user_passw_test;

  //* parametros
  // posibles condiciones de iva
  public $iva_condition_params = [
    'responsable inscripto',
    'monotributista',
    'iva excento'
  ];

  //* guardar un proveedor con direccion y usuario
  public function save()
  {

    // validar proveedor, direccion y credenciales de usuario
    // todo: validacion corecta del cuit, formato adecuado.
    $this->validate([

      'user_name'       => 'required|regex:/^[\p{L}\p{N}\s]+$/u|max:50',
      'user_email'      => 'required|unique:users,email|max:90',
      'user_passw'      => 'required|min:8|max:20',
      'user_passw_test' => 'required|min:8|max:20|same:user_passw',

      'company_name'  => 'required|regex:/^[\p{L}\p{N}\s]+$/u|unique:suppliers,company_name|max:50',
      'company_cuit'  => 'required|unique:suppliers,company_cuit|size:11',
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
      'user_passw'      => 'contraseña',
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

    // todo: manejo de errores, transaccion para crear todo o abortar

    // crear direccion
    $supplier_address = Address::create([
      'street'      => $this->company_street,
      'number'      => $this->company_number,
      'postal_code' => $this->company_postal_code,
      'city'        => $this->company_city,
    ]);

    // crear usuario
    // todo: usar razon social como nombre de usuario, formato slug
    // todo: generar contrasenia aleatoria
    $supplier_user = User::create([
      'name'      => $this->user_name,
      'email'     => $this->user_email,
      'password'  => bcrypt($this->user_passw),
    ]);

    // asignar rol de proveedor
    $supplier_user->assignRole('proveedor');

    // crear proveedor
    Supplier::create([
      'company_name'        => $this->company_name,
      'company_cuit'        => $this->company_cuit,
      'iva_condition'       => $this->company_iva,
      'phone_number'        => $this->company_phone,
      'short_description'   => $this->company_short_desc,
      'user_id'             => $supplier_user->id,
      'address_id'          => $supplier_address->id
    ]);

    $this->reset(['company_name', 'company_cuit', 'company_iva', 'company_phone', 'company_short_desc', 'company_street', 'company_number', 'company_city', 'company_postal_code', 'user_name', 'user_email', 'user_passw', 'user_passw_test']);

    $this->redirectRoute('suppliers-suppliers-index');
  }

  public function render()
  {
      return view('livewire.suppliers.create-supplier');
  }
}
