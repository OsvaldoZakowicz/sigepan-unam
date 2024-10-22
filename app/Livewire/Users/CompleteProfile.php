<?php

namespace App\Livewire\Users;

use App\Models\Address;
use App\Models\Gender;
use App\Models\Profile;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CompleteProfile extends Component
{
  // usuario actual y generos disponibles
  public $user;
  public $user_profile;
  public $user_address;
  // los generos disponibles
  public $genders;

  // perfil de usuario
  public $first_name;
  public $last_name;
  public $dni;
  public $birthdate;
  public $phone_number;
  public $user_gender; // al crear
  public $user_gender_update; // al actulizar
  public $street;
  public $number;
  public $city;
  public $postal_code;

  public function mount()
  {
    $this->user = Auth::user();
    $this->genders = Gender::all();

    //* si tiene perfil completo, montar datos
    // si profile() no tiene registro, retorna null, evalua como false
    // false indica que no tiene perfil completo.
    if ($this->user->profile) {

      // perfil
      $this->user_profile = $this->user->profile;

      // datos del perfil
      $this->first_name   = $this->user_profile->first_name;
      $this->last_name    = $this->user_profile->last_name;
      $this->dni          = $this->user_profile->dni;
      $this->birthdate    = $this->user_profile->birthdate;
      $this->phone_number = $this->user_profile->phone_number;
      $this->user_gender_update  = $this->user_profile->gender->id; //objeto

      // datos de la direccion
      $this->user_address = $this->user_profile->address;

      $this->street       = $this->user_address->street;
      $this->number       = $this->user_address->number;
      $this->city         = $this->user_address->city;
      $this->postal_code  = $this->user_address->postal_code;
    }

  }

  public function save()
  {
    //* si tiene perfil ya completo, actualizar
    if ($this->user->profile) {

      /* dd(['user_gender_update' => $this->user_gender_update]); */

      //validaciones para actualizar
      $this->validate([
        'first_name' =>   ['required','max:50','string'],
        'last_name' =>    ['required','max:50','string'],
        'dni' =>          ['required',Rule::unique('profiles','dni')->ignore($this->user_profile->id),'min:8','numeric'],
        'birthdate' =>    ['required'],
        'phone_number' => ['required',Rule::unique('profiles','phone_number')->ignore($this->user_profile->id),'min:10'],
        'user_gender_update'  => ['required'],
        'street' => ['required','max:45'],
        'number' => ['nullable','max:8'],
        'city' =>   ['required','max:45'],
        'postal_code' => ['required','min:4'],
      ],[],[
        'first_name'    => 'nombres',
        'last_name'     => 'apellidos',
        'dni'           => 'dni',
        'birthdate'     => 'fecha de nacimiento',
        'phone_number'  => 'telefono',
        'user_gender_update'  => 'genero',
        'street'  => 'calle',
        'number'  => 'numero de calle',
        'city'    => 'ciudad',
        'postal_code' => 'codigo postal',
      ]);

      /* dd(['user_gender_update' => $this->user_gender]); */

      // actualizar direccion
      $this->user_address->street       = $this->street;
      $this->user_address->number       = $this->number;
      $this->user_address->city         = $this->city;
      $this->user_address->postal_code  = $this->postal_code;
      $this->user_address->save();

      // actualizar perfil
      $this->user_profile->first_name   = $this->first_name;
      $this->user_profile->last_name    = $this->last_name;
      $this->user_profile->dni          = $this->dni;
      $this->user_profile->birthdate    = $this->birthdate;
      $this->user_profile->phone_number = $this->phone_number;
      $this->user_profile->gender_id    = $this->user_gender_update; //id nuevo si cambia de genero
      $this->user_profile->save();

    } else {

      /* dd(['user_gender_save' => $this->user_gender]); */

      //validaciones
      $this->validate([
        'first_name' =>   ['required','max:50','string'],
        'last_name' =>    ['required','max:50','string'],
        'dni' =>          ['required','unique:profiles','min:8','numeric'],
        'birthdate' =>    ['required'],
        'phone_number' => ['required','unique:profiles','min:10'],
        'user_gender' => ['required'],
        'street' => ['required','max:45'],
        'number' => ['nullable','max:8'],
        'city' =>   ['required','max:45'],
        'postal_code' => ['required','min:4'],
      ],[],[
        'first_name'    => 'nombres',
        'last_name'     => 'apellidos',
        'dni'           => 'dni',
        'birthdate'     => 'fecha de nacimiento',
        'phone_number'  => 'telefono',
        'user_gender'   => 'genero',
        'street'  => 'calle',
        'number'  => 'numero de calle',
        'city'    => 'ciudad',
        'postal_code' => 'codigo postal',
      ]);

      /* dd(['user_gender_save' => $this->user_gender]); */

      // direccion
      $new_address = Address::create([
        'street' => $this->street,
        'number' => $this->number,
        'city' => $this->city,
        'postal_code' => $this->postal_code
      ]);

      // perfil
      Profile::create([
        'first_name' => $this->first_name,
        'last_name' => $this->last_name,
        'dni' => $this->dni,
        'birthdate' => $this->birthdate,
        'phone_number' => $this->phone_number,
        'gender_id' => $this->user_gender, // al guardar, viene como id string
        'address_id' => $new_address->id, // direccion creada
        'user_id' => $this->user->id
      ]);

    }

    $this->redirectRoute('profile');
  }

  public function render()
  {
      return view('livewire.users.complete-profile');
  }
}
