<?php

namespace App\Livewire\Users;

use App\Models\Address;
use App\Models\Gender;
use App\Models\Profile;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CompleteProfile extends Component
{
  // usuario actual y generos disponibles
  public $user;
  public $genders;

  // perfil de usuario
  public $first_name;
  public $last_name;
  public $dni;
  public $birthdate;
  public $phone_number;
  public $gender;
  public $street;
  public $number;
  public $city;
  public $postal_code;

  public function mount()
  {
    $this->user = Auth::user();
    $this->genders = Gender::all();

    // todo: si tiene perfil actualizado, mostrar
    // todo: relaciones entre modelos

  }

  public function save()
  {
    //validaciones
    $this->validate([
      'first_name' =>   ['required','max:50','string'],
      'last_name' =>    ['required','max:50','string'],
      'dni' =>          ['required','min:8','numeric'],
      'birthdate' =>    ['required'],
      'phone_number' => ['required','min:10'],
      'gender' => ['required'],
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
      'gender'  => 'genero',
      'street'  => 'calle',
      'number'  => 'numero de calle',
      'city'    => 'ciudad',
      'postal_code' => 'codigo postal',
    ]);

    // perfil
    $new_profile = Profile::create([
      'first_name' => $this->first_name,
      'last_name' => $this->last_name,
      'dni' => $this->dni,
      'birthdate' => $this->birthdate,
      'phone_number' => $this->phone_number,
      'gender_id' => $this->gender,
      'user_id' => $this->user->id
    ]);

    // direccion
    $new_address = Address::create([
      'street' => $this->street,
      'number' => $this->number,
      'city' => $this->city,
      'postal_code' => $this->postal_code,
      'profile_id' => $new_profile->id
    ]);

    // limpiar propiedades
    $this->reset(
      [
      'first_name',
      'last_name',
      'dni',
      'birthdate',
      'phone_number',
      'gender',
      'street',
      'number',
      'city',
      'postal_code'
      ]
    );

    $this->dispatch('profile-updated');
  }

  public function render()
  {
      return view('livewire.users.complete-profile');
  }
}
