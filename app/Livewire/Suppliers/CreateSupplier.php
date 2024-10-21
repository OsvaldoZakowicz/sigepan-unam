<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use App\Models\Gender;
use Illuminate\Support\Facades\Validator;

class CreateSupplier extends Component
{
  // proveedor entidad
  public $company_name;
  public $company_cuit;
  public $company_iva;
  public $company_phone;
  public $company_short_desc;

  // proveedor direccion
  public $company_street;
  public $company_number;
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
  // generos
  public $genders;

  public function mount()
  {
    $this->genders = Gender::all();
  }

  /**
   * * guardar un proveedor.
   * todo: validacion corecta del cuit.
   */
  public function save()
  {

    $validated = $this->validate([

      'user_name'       => 'required|regex:/^[\p{L}\p{N}\s]+$/u|max:50',
      'user_email'      => 'required|max:90',
      'user_passw'      => 'required|min:8|max:20',
      'user_passw_test' => 'required|min:8|max:20',

      'company_name'  => 'required|regex:/^[\p{L}\p{N}\s]+$/u|max:50',
      'company_cuit'  => 'required',
      'company_iva'   => 'required',
      'company_phone' => 'required|min:10',
      'company_short_desc' => 'required|max:150',

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

      'company_street'  => 'calle',
      'company_number'  => 'numero de calle',
      'company_city'    => 'ciudad',
      'company_postal_code'  => 'codigo postal'

    ]);

    dd($validated);
  }

  public function render()
  {
      return view('livewire.suppliers.create-supplier');
  }
}
