<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use App\Services\User\UserService;

class CreateUser extends Component
{
  public $roles;
  public $user_name;
  public $user_email;
  public $user_password;
  public $user_role;

  //* montar datos
  public function mount(UserService $user_service)
  {
    // recuperar roles para el checkbox
    $this->roles = Role::where($user_service->getInternalRoleAttribute(), true)
      ->where('name', '!=', $user_service->getRestrictedRole())
      ->get();
  }

  //* crear usuario
  //* usar inyeccion de dependencia de laravel para traer una instancia del servicio
  public function save(UserService $user_service)
  {
    $validated = $this->validate([
      'user_name' => 'required|max:50|regex:/^[a-zA-Z\s]+$/u',
      'user_email' => 'required|email|unique:users,email',
      'user_role' => 'required',
    ], [
      'user_name.regex' => 'El :attribute debe contener letras y espacios solamente',
    ], [
      'user_name' => 'nombre de usuario',
      'user_email' => 'email',
      'user_role' => 'rol'
    ]);

    try {

      $validated += ['user_password' => randomPassword()];
      $user_service->createInternalUser($validated);

      //todo: si la creacion es correcta:
      // - enviar acceso via email al nuevo usuario

      $this->reset();

      session()->flash('operation-success', toastSuccessBody('usuario', 'creado'));
      $this->redirectRoute('users-users-index');

    } catch (\Exception $e) {

      session()->flash('operation-error', 'error: ' . $e->getMessage() . ', contacte con el Administrador');
      $this->redirectRoute('users-users-index');

    }

  }

  public function render()
  {
    return view('livewire.users.create-user');
  }
}
